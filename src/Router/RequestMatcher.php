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

        $module = $params['module'];
        unset($params['module']);

        $controller = $params['controller'];
        unset($params['controller']);

        $action = $params['action'];
        unset($params['action']);

        return new MvcRequest($routeData->getRouteName(), $module, $controller, $action, $params);
    }
}
