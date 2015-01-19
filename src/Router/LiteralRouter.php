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
        if ($httpRequest->getPath() !== $this->route) {
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
        return $this->route;
    }
}
