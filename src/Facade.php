<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Application;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;
use Gobline\Container\Container;
use Gobline\Environment\Environment;
use Gobline\Registrar\Registrar;
use Gobline\View\Helper\ViewHelperRegistry;
use Gobline\Router\RouteCollection;
use Gobline\Flash\Flash;
use Gobline\Auth\CurrentUser;
use Gobline\Auth\CurrentUserInterface;
use Gobline\Auth\Persistence\CurrentUser as SessionDecorator;
use Gobline\Application\Middleware\MiddlewareDispatcher;
use Gobline\Application\Middleware\RequestMatcherMiddleware;
use Gobline\Application\Middleware\AuthorizerMiddleware;
use Gobline\Application\Middleware\DispatcherMiddleware;
use Gobline\Application\Middleware\HtmlRendererMiddleware;
use Gobline\Application\Middleware\ErrorHandler\NotFoundHandler;
use Gobline\Application\Middleware\ErrorHandler\NotAuthenticatedHandler;
use Gobline\Application\Middleware\ErrorHandler\NotAuthorizedHandler;
use Gobline\Application\Middleware\ErrorHandler\ErrorHandler;
use Gobline\Router\Exception\NoMatchingRouteException;
use Gobline\Auth\Exception\NotAuthenticatedException;
use Gobline\Acl\Exception\NotAuthorizedException;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Facade
{
    private $container;
    private $request;
    private $response;

    public function __construct()
    {
        $this->container = new Container();
        $this->container->registerSelf();

        $this->request = ServerRequestFactory::fromGlobals();
        $this->response = new Response();

        $this->registerEnvironment();
        $this->registerRouteCollection();
        $this->registerFlash();
        $this->registerAuth();
        $this->registerViewHelperRegistry();
        $this->registerDispatcher();

        $this->addDefaultMiddlewares();
    }

    public function run()
    {
        $response = $this->getDispatcher()->dispatch($this->request, $this->response);

        if (headers_sent()) {
            echo $response->getBody();
        } else {
            (new SapiEmitter())->emit($response);
        }
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getRegistrar()
    {
        return new Registrar($this->container);
    }

    public function getViewHelperRegistry()
    {
        return $this->container->get(ViewHelperRegistry::class);
    }

    public function getDispatcher()
    {
        return $this->container->get(MiddlewareDispatcher::class);
    }

    public function getRouteCollection()
    {
        return $this->container->get(RouteCollection::class);
    }

    public function setDebugMode($debugMode)
    {
        $this->container->get(Environment::class)->setDebugMode($debugMode);
    }

    private function registerEnvironment()
    {
        $environment = new Environment();
        $environment
            ->setRequest($this->request)
            ->setResponse($this->response)
            ->setBasePathResolver('auto');

        $this->container->share($environment);
    }

    private function registerRouteCollection()
    {
        $this->container->share(new RouteCollection());
    }

    private function registerFlash()
    {
        $flash = new Flash();
        $flash->initialize();

        $this->container->share($flash);
    }

    private function registerAuth()
    {
        $auth = new SessionDecorator(new CurrentUser());

        $this->container
            ->share($auth)
            ->alias(CurrentUserInterface::class, $auth);
    }

    private function registerViewHelperRegistry()
    {
        $this->container->share(new ViewHelperRegistry($this->container));
    }

    private function registerDispatcher()
    {
        $this->container->share(MiddlewareDispatcher::class);
    }

    private function addDefaultMiddlewares()
    {
        $this->getDispatcher()
            ->addMiddleware(RequestMatcherMiddleware::class)
            ->addMiddleware(AuthorizerMiddleware::class)
            ->addMiddleware(DispatcherMiddleware::class)
            ->addMiddleware(HtmlRendererMiddleware::class)
            ->addErrorHandler(NoMatchingRouteException::class, NotFoundHandler::class)
            ->addErrorHandler(NotAuthenticatedException::class, NotAuthenticatedHandler::class)
            ->addErrorHandler(NotAuthorizedException::class, NotAuthorizedHandler::class)
            ->addErrorHandler(\Exception::class, ErrorHandler::class);
    }
}
