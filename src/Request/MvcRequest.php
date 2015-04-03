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

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class MvcRequest
{
    private $route;
    private $module;
    private $controller;
    private $action;
    private $template;
    private $params;
    private $forwarded = 0;
    private $dispatched = false;
    private $subRequest = false;

    public function __construct($route, $module, $controller, $action, array $params = [], $template = null)
    {
        $this->route = (string) $route;
        if ($this->route === '') {
            throw new \InvalidArgumentException('$route cannot be empty');
        }

        $this->module = $module;

        $this->controller = $controller;

        $this->action = $action;

        $this->params = $params;

        $this->template = ($template) ?: $controller;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $route = (string) $route;
        if ($route === '') {
            throw new \InvalidArgumentException('$route cannot be empty');
        }

        $this->route = $route;
    }

    public function getModule($camelCase = false)
    {
        return $camelCase ? $this->toCamelCase($this->module) : $this->module;
    }

    public function setModule($module)
    {
        $module = (string) $module;
        if ($module === '') {
            throw new \InvalidArgumentException('$module cannot be empty');
        }

        $this->module = $module;
    }

    public function getController($camelCase = false)
    {
        return $camelCase ? $this->toCamelCase($this->controller) : $this->controller;
    }

    public function setController($controller)
    {
        $controller = (string) $controller;
        if ($controller === '') {
            throw new \InvalidArgumentException('$controller cannot be empty');
        }

        $this->controller = $controller;
    }

    public function getAction($camelCase = false)
    {
        return $camelCase ? $this->toCamelCase($this->action) : $this->action;
    }

    public function setAction($action)
    {
        $action = (string) $action;
        if ($action === '') {
            throw new \InvalidArgumentException('$action cannot be empty');
        }

        $this->action = $action;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $template = (string) $template;
        if ($template === '') {
            throw new \InvalidArgumentException('$template cannot be empty');
        }

        $this->template = $template;
    }

    public function hasParam($name)
    {
        if ((string) $name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        return array_key_exists($name, $this->params);
    }

    public function getParam(...$args)
    {
        switch (count($args)) {
            default:
                throw new \InvalidArgumentException('getParam() takes one or two arguments');
            case 1:
                if (!$this->hasParam($args[0])) {
                    throw new \InvalidArgumentException('Route parameter "'.$args[0].'" not found');
                }
                return $this->params[$args[0]];
            case 2:
                if (!$this->hasParam($args[0])) {
                    return $args[1];
                }
                return $this->params[$args[0]];
        }
    }

    public function setParam($name, $value)
    {
        $name = (string) $name;
        if ($name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        $this->params[$name] = $value;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function isForwarded()
    {
        return $this->forwarded;
    }

    public function setForwarded()
    {
        ++$this->forwarded;
    }

    public function isDispatched()
    {
        return $this->dispatched;
    }

    public function setDispatched()
    {
        $this->dispatched = true;
    }

    public function isSubRequest()
    {
        return $this->subRequest;
    }

    public function setSubRequest()
    {
        $this->subRequest = true;
    }

    public function isInternal()
    {
        return (bool) $this->forwarded ?: $this->subRequest ?: false;
    }

    private function toCamelCase($str)
    {
        return ucfirst(str_replace(' ', '', ucwords(strtolower(str_replace('-', ' ', $str)))));
    }

    private function startsWith($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }

    public function __toString()
    {
        $str = $this->module.'/'.$this->controller.'/'.$this->action;
        foreach ($this->params as $key => $value) {
            $str .= '/'.$key.'/'.$value;
        }

        return $str;
    }

    public function copy(MvcRequest $object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            $this->$key = $value;
        }
    }
}
