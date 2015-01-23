<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Router\I18n;

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
    private $translatePlaceholderValues;

    public function __construct($name, $route, array $defaults, array $constraints = [], $translatePlaceholderValues = false)
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
        $this->translatePlaceholderValues = $translatePlaceholderValues;
    }

    public function setTranslatePlaceholderValues($translatePlaceholderValues)
    {
        $this->translatePlaceholderValues = $translatePlaceholderValues;
    }

    public function getName()
    {
        return $this->name;
    }

    public function match(HttpRequestInterface $httpRequest)
    {
        $language = $httpRequest->getLanguage();

        if (!$language || !$this->translator->hasTranslations($language)) {
            return false;
        }

        $requestSegments = $this->getSegments($httpRequest->getPath());

        if (!$requestSegments) {
            return false;
        }

        $translations = array_flip($this->translator->getTranslations($language));

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
        $requiredRouteSegments = [];
        $requiredRoutePart = array_shift($route);
        if ($requiredRoutePart !== '') {
            $requiredRouteSegments = explode('/', ltrim($requiredRoutePart, '/'));
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

                if ($routeSegment[0] === '*') {
                    $routeSegment = ltrim($routeSegment, '*');

                    if (!$routeSegment) {
                        $requestSegments = [];
                        break;
                    }

                    if ($routeSegment[0] !== ':') {
                        throw new \Exception('Invalid route format: '.$this->route);
                    }

                    $routeSegment = ltrim($routeSegment, ':');
                    $requestSegment = implode('/', $requestSegments);


                    if ($this->translatePlaceholderValues) {
                        $requestSegment = isset($translations[$requestSegment]) ? $translations[$requestSegment] : $requestSegment;
                    }

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

                    $params[$routeSegment] = $requestSegment;

                    $requestSegments = [];
                    break;
                }

                $requestSegment = array_shift($requestSegments);

                $isPlaceholder = ($routeSegment[0] === ':');

                if ($isPlaceholder) {
                    $routeSegment = ltrim($routeSegment, ':');

                    switch ($routeSegment) {
                        default:
                            if ($this->translatePlaceholderValues) {
                                $requestSegment = isset($translations[$requestSegment]) ? $translations[$requestSegment] : $requestSegment;
                            }
                            $params[$routeSegment] = $requestSegment;
                            break;
                        case 'module':
                        case 'controller':
                        case 'action':
                            $requestSegment = isset($translations[$requestSegment]) ? $translations[$requestSegment] : $requestSegment;
                            $$routeSegment = $requestSegment;
                            break;
                    }

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
                } else {
                    $requestSegment = isset($translations[$requestSegment]) ? $translations[$requestSegment] : $requestSegment;

                    if ($routeSegment !== $requestSegment) {
                        return false;
                    }
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
        $placeholders = [];

        if ($request->getModule()) {
            $placeholders[':module'] = $this->translator->translate($request->getModule(), null, $language);
        }

        if ($request->getController()) {
            $placeholders[':controller'] = $this->translator->translate($request->getController(), null, $language);
        }

        if ($request->getAction()) {
            $placeholders[':action'] = $this->translator->translate($request->getAction(), null, $language);
        }

        foreach ($request->getParams() as $name => $value) {
            if ($this->translatePlaceholderValues) {
                $value = $this->translator->translate($value, null, $language);
            }
            $placeholders[':'.$name] = $value;
        }

        $url = '';

        $route = explode('[/', $this->route);
        $requiredRoutePart = array_shift($route);

        if ($requiredRoutePart) {
            $requiredRouteSegments = explode('/', ltrim($requiredRoutePart, '/'));

            foreach ($requiredRouteSegments as &$routeSegment) {
                if ($routeSegment[0] === ':') {
                    continue;
                }
                $routeSegment = $this->translator->translate($routeSegment, null, $language);
            }

            $requiredRoutePart = '/'.implode('/', $requiredRouteSegments);

            $url = str_replace(array_keys($placeholders), array_values($placeholders), $requiredRoutePart);

            if (strpos($url, ':') !== false) {
                throw new \Exception('parameters are missing');
            }

            if (!$route) {
                return str_replace('*', '', $url);
            }
        }

        $rtrim = function ($value) { return rtrim($value, ']'); };
        $optionalRouteParts = array_map($rtrim, $route);

        $defaults = [];

        foreach ($this->defaults as $k => $v) {
            $defaults[':'.$k] = $v;
        }

        $tmpUrl = '';

        foreach ($optionalRouteParts as $routePart) {
            $routeSegments = explode('/', $routePart);

            foreach ($routeSegments as &$routeSegment) {
                if ($routeSegment[0] === ':') {
                    continue;
                }
                $routeSegment = $this->translator->translate($routeSegment, null, $language);
            }
            $routePart = implode('/', $routeSegments);

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

        return str_replace('*', '', $url);
    }
}
