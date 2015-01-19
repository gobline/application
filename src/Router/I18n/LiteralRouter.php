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
class LiteralRouter extends AbstractRouter
{
    private $name;
    private $route;
    private $defaults;

    public function __construct($name, $route, array $defaults)
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

        $routeSegments = $this->getSegments($this->route);
        do {
            $requestSegment = array_shift($requestSegments);
            $requestSegment = isset($translations[$requestSegment]) ? $translations[$requestSegment] : $requestSegment;
            if (array_shift($routeSegments) !== $requestSegment) {
                return false;
            }
        } while ($requestSegments);

        if ($routeSegments) {
            return false;
        }

        $defaults = $this->defaults;

        $module = isset($defaults['module']) ? $defaults['module'] : $this->defaultModule;
        $controller = isset($defaults['controller']) ? $defaults['controller'] : 'index';
        $action = isset($defaults['action']) ? $defaults['action'] : 'index';

        unset($defaults['module']);
        unset($defaults['controller']);
        unset($defaults['action']);

        return new MvcRequest($this->name, $module, $controller, $action, $defaults);
    }

    public function makeUrl(MvcRequest $request, $language = null)
    {
        $routeSegments = $this->getSegments($this->route);

        $route = '';
        do {
            $route .= '/'.$this->translator->translate(array_shift($routeSegments), null, $language);
        } while ($routeSegments);

        return $route;
    }
}
