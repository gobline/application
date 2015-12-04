<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Application\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Relay\RelayBuilder;
use Gobline\Container\ContainerInterface;
use Gobline\Environment\Environment;
use Gobline\Application\Middleware\ErrorHandler\WhoopsHandler;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\Response\EmptyResponse;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class MiddlewareDispatcher
{
    private $container;
    private $environment;
    private $middlewares = [];
    private $errorHandlers = [];
    private $relayBuilder;
    private $relay;

    public function __construct(ContainerInterface $container, Environment $environment)
    {
        $this->container = $container;
        $this->environment = $environment;

        $resolver = function ($className) use ($container) {
            return $container->get($className);
        };
        $this->relayBuilder = new RelayBuilder($resolver);
    }

    public function dispatch(ServerRequestInterface $request, ResponseInterface $response, $suppressErrors = false)
    {
        if (!$this->relay) {
            $this->relay = $this->relayBuilder->newInstance($this->middlewares);
        }

        try {
            $response = $this->relay->__invoke($request, $response);
        } catch (\Exception $e) {
            if ($this->environment->isDebugMode()) {
                $handler = $this->container->get(WhoopsHandler::class);
            } else {
                foreach ($this->errorHandlers as $exceptionClassName => $handlerClassName) {
                    if (is_a($e, $exceptionClassName)) {
                        $handler = $this->container->get($handlerClassName);
                        break;
                    }
                }
            }
            $response = $handler($request, $response, $e);

            if ($suppressErrors) {
                return new EmptyResponse();
            }

            while (ob_get_level()) {
                if ($this->environment->isDebugMode()) {
                    echo ob_get_clean();
                } else {
                    ob_end_clean();
                }
            }
            if (headers_sent()) {
                echo $response->getBody();
            } else {
                (new SapiEmitter())->emit($response);
            }
            exit;
        }

        return $response;
    }

    public function addErrorHandler($exceptionClassName, $handlerClassName)
    {
        $this->errorHandlers[$exceptionClassName] = $handlerClassName;
    }

    public function addMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function setErrorHandlers(array $handlers)
    {
        foreach ($handlers as $exceptionClassName => $handlerClassName) {
            $this->addErrorHandler($exceptionClassName, $handlerClassName);
        }
    }

    public function setMiddlewares(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }
}
