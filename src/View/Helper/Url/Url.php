<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Url;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\Router\UrlMaker;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Url implements ViewHelperInterface
{
    private $urlMaker;
    private $request;

    public function __construct(UrlMaker $urlMaker, MvcRequest $request)
    {
        $this->urlMaker = $urlMaker;
        $this->request = $request;
    }

    public function url($path, $data = null, $language = null, $absolute = false)
    {
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

        $request = new MvcRequest('default', $module, $controller, $action, $data);

        return $this->urlMaker->makeUrl($request, $language, $absolute);
    }

    public function __call($name, array $arguments)
    {
        array_unshift($arguments, $name);
        return $this->route(...$arguments);
    }

    public function route($route, $data, $language = null, $absolute = false)
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

        return $this->urlMaker->makeUrl($request, $language, $absolute);
    }

    public function self($data = [], $language = null, $absolute = false)
    {
        $module = $this->request->getModule();
        $controller = $this->request->getController();
        $action = $this->request->getAction();

        if (!is_array($data)) {
            $data = $this->makeKeyValuePairs(explode('/', $data));
        }

        $request = new MvcRequest('default', $module, $controller, $action, $data);

        return $this->urlMaker->makeUrl($request, $language, $absolute);
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
