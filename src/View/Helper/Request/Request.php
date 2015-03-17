<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Request;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\Request\Hmvc;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Request implements ViewHelperInterface
{
    private $hmvc;

    public function __construct(Hmvc $hmvc, MvcRequest $request)
    {
        $this->hmvc = $hmvc;
        $this->request = $request;
    }

    public function __call($name, array $arguments)
    {
        array_unshift($arguments, $name);
        return $this->route(...$arguments);
    }

    public function route($route, $data, $language = null)
    {
        if (!is_array($data)) {
            $data = $this->makeKeyValuePairs(explode('/', $data));
        }

        if (isset($data['module'])) {
            $module = $data['module'];
            unset($data['module']);
        } else {
            $module = $this->request->getModule();
        }

        if (isset($data['controller'])) {
            $controller = $data['controller'];
            unset($data['controller']);
        } else {
            $controller = $this->request->getController();
        }

        if (isset($data['action'])) {
            $action = $data['action'];
            unset($data['action']);
        } else {
            $action = $this->request->getAction();
        }

        $request = new MvcRequest($route, $module, $controller, $action, $data);

        return $this->hmvc->execute($request, $language);
    }

    public function url($path, $data = null, $language = null)
    {
        $path = explode(':', $path);
        switch (count($path)) {
            case 2:
                $route = $path[0];
                $path = $path[1];
                break;
            case 1:
                $route = 'default';
                $path = $path[0];
                break;
            default:
                throw new \InvalidArgumentException('$path invalid');
        }

        $path = explode('/', $path);
        switch (count($path)) {
            case 3:
                $module = $path[0];
                $controller = $path[1];
                $action = $path[2];
                break;
            case 2:
                $module = $this->request->getModule();
                $controller = $path[0];
                $action = $path[1];
                break;
            case 1:
                $module = $this->request->getModule();
                $controller = $path[0];
                $action = 'index';
                break;
            default:
                throw new \InvalidArgumentException('$path invalid');
        }

        if (!$data) {
            $data = [];
        } elseif (!is_array($data)) {
            $data = $this->makeKeyValuePairs(explode('/', $data));
        }

        $request = new MvcRequest($route, $module, $controller, $action, $data);

        return $this->hmvc->execute($request, $language);
    }

    public function request()
    {
        return $this;
    }

    private function makeKeyValuePairs(array $array)
    {
        $pairs = [];
        $nb = count($array);
        for ($i = 0; $i < $nb - 1; $i += 2) {
            $pairs[$array[$i]] = $array[$i+1];
        }
        if ($i < $nb) {
            $pairs[$array[$i]] = '';
        }

        return $pairs;
    }
}
