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

        $resolver = function ($middleware) use ($container) {
            if (is_string($middleware)) {
                return $container->get($middleware);
            }

            return $middleware;
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
            $this->handleException($request, $response, $e, $suppressErrors);
        }

        return $response;
    }

    public function handleException(ServerRequestInterface $request, ResponseInterface $response, \Throwable $e, $suppressErrors = false)
    {
        if ($this->environment->isDebugMode()) {
            $handler = $this->container->get(WhoopsHandler::class);
        } else {
            foreach ($this->errorHandlers as $exceptionClassName => $handler) {
                if (is_a($e, $exceptionClassName)) {
                    if (is_string($handler)) {
                        $handler = $this->container->get($handler);
                    }
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

    public function addErrorHandler($exceptionClassName, $handler)
    {
        $this->errorHandlers[$exceptionClassName] = $handler;

        return $this;
    }

    public function addMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function setErrorHandlers(array $handlers)
    {
        foreach ($handlers as $exceptionClassName => $handler) {
            $this->addErrorHandler($exceptionClassName, $handler);
        }
    }

    public function setMiddlewares(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    public function hasMiddlewares()
    {
        return (bool) $this->middlewares;
    }

    public function hasErrorHandlers()
    {
        return (bool) $this->errorHandlers;
    }
}
