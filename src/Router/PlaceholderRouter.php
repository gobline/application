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
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class PlaceholderRouter extends AbstractRouter
{
    private $name;
    private $route;
    private $defaults;
    private $constraints;

    public function __construct($name, $route, array $defaults, array $constraints = [])
    {
        $this->name = (string) $name;
        if ($this->name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        $this->route = (string) $route;
        if ($this->route === '') {
            throw new \InvalidArgumentException('$route cannot be empty');
        }

        $this->defaults = $defaults;
        $this->constraints = $constraints;
    }

    public function getName()
    {
        return $this->name;
    }

    public function match(HttpRequestInterface $httpRequest)
    {
        $requestSegments = $this->getSegments($httpRequest->getPath());

        if (!$requestSegments) {
            return false;
        }

        $defaults = $this->defaults;

        $module = isset($defaults['module']) ? $defaults['module'] : $this->defaultModule;
        $controller = isset($defaults['controller']) ? $defaults['controller'] : 'index';
        $action = isset($defaults['action']) ? $defaults['action'] : 'index';

        unset($defaults['module']);
        unset($defaults['controller']);
        unset($defaults['action']);
        $params = $defaults;

        $route = explode('[/', $this->route);

        // required segments
        $requiredRouteSegments = array_shift($route);

        if ($requiredRouteSegments === '') {
            $requiredRouteSegments = [];
        } else {
            $requiredRouteSegments = explode('/', ltrim($requiredRouteSegments, '/'));
        }

        // optional segments
        $rtrim = function ($value) { return rtrim($value, ']'); };
        $optionalRouteParts = array_map($rtrim, $route);

        /*
        * Example route: /hello/:world[/:foo/bar[/:qux[/:corge]]]
        * $requiredRouteSegments contains the segments [hello, :world]
        * $optionalRouteParts contains the parts [:foo/bar, :qux, :corge]
        */

        while ($requiredRouteSegments || $optionalRouteParts) {
            if (!$requestSegments) {
                if ($requiredRouteSegments) {
                    return false;
                } else {
                    break;
                }
            }

            if ($requiredRouteSegments) {
                $routePart = array_shift($requiredRouteSegments);
            } else {
                $routePart = array_shift($optionalRouteParts);
            }

            $routeSegments = explode('/', $routePart);

            foreach ($routeSegments as $routeSegment) {
                if (!$requestSegments) {
                    return false;
                }

                $requestSegment = array_shift($requestSegments);
                $isPlaceholder = ($routeSegment[0] === ':');

                if ($isPlaceholder) {
                    $routeSegment = ltrim($routeSegment, ':');

                    if (isset($this->constraints[$routeSegment])) {
                        $result = preg_match($this->constraints[$routeSegment], $requestSegment);

                        if ($result === false) {
                            throw new \Exception('Pattern "'.$this->constraints[$routeSegment].
                                '" gave an error for value "'.$requestSegment.'"');
                        }

                        if (!$result) {
                            return false;
                        }
                    }

                    switch ($routeSegment) {
                        default:
                            $params[$routeSegment] = $requestSegment;
                            break;
                        case 'module':
                            $module = $requestSegment;
                            break;
                        case 'controller':
                            $conroller = $requestSegment;
                            break;
                        case 'action':
                            $action = $requestSegment;
                            break;
                    }
                } elseif ($routeSegment !== $requestSegment) {
                    return false;
                }
            }
        }

        if ($requestSegments) {
            return false;
        }

        return new MvcRequest($this->name, $module, $controller, $action, $params);
    }

    public function makeUrl(MvcRequest $request, $language = null)
    {
        $url = '';

        $route = explode('[/', $this->route);
        $requiredRoutePart = array_shift($route);

        if ($request->getModule()) {
            $placeholders[':module'] = $request->getModule();
        }

        if ($request->getController()) {
            $placeholders[':controller'] = $request->getController();
        }

        if ($request->getAction()) {
            $placeholders[':action'] = $request->getAction();
        }

        foreach ($request->getParams() as $name => $value) {
            $placeholders[':'.$name] = $value;
        }

        $url .= str_replace(array_keys($placeholders), array_values($placeholders), $requiredRoutePart);

        if (strpos($url, ':') !== false) {
            throw new \Exception('parameters are missing');
        }

        if (!$route) {
            return $url;
        }

        $rtrim = function ($value) { return rtrim($value, ']'); };
        $optionalRouteParts = array_map($rtrim, $route);

        $defaults = [];

        foreach ($this->defaults as $k => $v) {
            $defaults[':'.$k] = $v;
        }

        $tmpUrl = '';

        foreach ($optionalRouteParts as $routePart) {
            $urlPart = str_replace(array_keys($placeholders), array_values($placeholders), $routePart);
            $replaced = ($urlPart !== $routePart);

            if (strpos($urlPart, ':') !== false) {
                $urlPart = str_replace(array_keys($defaults), array_values($defaults), $urlPart);
            }

            if (strpos($urlPart, ':') !== false) {
                if ($replaced) {
                    throw new \Exception('parameters are missing');
                }
                break;
            }

            $tmpUrl .= '/'.$urlPart;

            if ($urlPart !== str_replace(array_keys($defaults), array_values($defaults), $routePart)) {
                $url .= $tmpUrl;
                $tmpUrl = '';
            }
        }

        return $url;
    }
}
