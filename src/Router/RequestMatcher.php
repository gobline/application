<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Router;

use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Router\RequestMatcherInterface;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class RequestMatcher
{
    private $requestMatcher;

    public function __construct(RequestMatcherInterface $requestMatcher)
    {
        $this->requestMatcher = $requestMatcher;
    }

    public function match(HttpRequestInterface $httpRequest)
    {
        $routeData = $this->requestMatcher->match($httpRequest);

        $params = $routeData->getParams();

        $module = isset($params['_module']) ? $params['_module'] : null;
        unset($params['_module']);

        $controller = isset($params['_controller']) ? $params['_controller'] : null;
        unset($params['_controller']);

        $action = isset($params['_action']) ? $params['_action'] : null;
        unset($params['_action']);

        $template = isset($params['_template']) ? $params['_template'] : null;
        unset($params['_template']);

        return new MvcRequest($routeData->getRouteName(), $module, $controller, $action, $params, $template);
    }
}
