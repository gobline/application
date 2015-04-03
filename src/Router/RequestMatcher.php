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

        $module = $params['_module'];
        unset($params['_module']);

        $controller = $params['_controller'];
        unset($params['_controller']);

        $action = $params['_action'];
        unset($params['_action']);

        return new MvcRequest($routeData->getRouteName(), $module, $controller, $action, $params);
    }
}
