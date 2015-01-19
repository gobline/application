<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Request;

use Mendo\Mediator\EventDispatcher;
use Mendo\Mvc\MvcLocator;
use Mendo\Mvc\View\Renderer\ViewRendererInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Dispatcher
{
    private $request;
    private $authorizer;
    private $mvcLocator;
    private $defaultViewRenderer;
    private $mustMapRouteParamsToActionArguments;

    public function __construct(
        MvcRequest $request,
        Authorizer $authorizer,
        MvcLocator $mvcLocator,
        ViewRendererInterface $defaultViewRenderer,
        EventDispatcher $eventDispatcher,
        $mustMapRouteParamsToActionArguments = true
    ) {
        $this->request = $request;
        $this->authorizer = $authorizer;
        $this->mvcLocator = $mvcLocator;
        $this->defaultViewRenderer = $defaultViewRenderer;
        $this->eventDispatcher = $eventDispatcher;
        $this->mustMapRouteParamsToActionArguments = (bool) $mustMapRouteParamsToActionArguments;
    }

    public function dispatch(ViewRendererInterface $viewRenderer = null)
    {
        $this->authorizer->authorize($this->request);

        $this->eventDispatcher->dispatch('beforeDispatch');

        $controller = $this->mvcLocator->getController($this->request);
        $viewModel = $this->mvcLocator->getViewModel($this->request);
        $controller->setViewModel($viewModel);

        $controller->init();

        $action = $this->request->getAction(true);

        if (method_exists($controller, $action)) {
            if ($this->mustMapRouteParamsToActionArguments) {
                call_user_func_array([$controller, $action], $this->request->getParams());
            } else {
                $controller->$action();
            }
        }

        $this->eventDispatcher->dispatch('afterDispatch');

        // the controller may have forwarded and dispatched the request already
        if ($this->request->isDispatched()) {
            return;
        }
        $this->request->setDispatched();

        $viewRenderer = $viewRenderer ?: $this->defaultViewRenderer;

        $viewRenderer->render($viewModel);
    }
}
