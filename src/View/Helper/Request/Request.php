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

        if (isset($data['_module'])) {
            $module = $data['_module'];
            unset($data['_module']);
        } else {
            $module = $this->request->getModule();
        }

        if (isset($data['_controller'])) {
            $controller = $data['_controller'];
            unset($data['_controller']);
        } else {
            $controller = $this->request->getController();
        }

        if (isset($data['_action'])) {
            $action = $data['_action'];
            unset($data['_action']);
        } else {
            $action = $this->request->getAction();
        }

        $request = new MvcRequest($route, $module, $controller, $action, $data);

        return $this->hmvc->execute($request, $language);
    }

    public function url($path, $data = null, $language = null)
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

        return $this->hmvc->execute($request, $language);
    }

    public function template($template, $language = null)
    {
        $template = explode(':', $template);
        switch (count($template)) {
            case 2:
                $module = $template[0];
                $template = $template[1];
                break;
            case 1:
                $module = null;
                $template = $template[0];
                break;
            default:
                throw new \InvalidArgumentException('$path invalid');
        }

        $request = new MvcRequest('template', $module, null, null, [], $template);

        return $this->hmvc->execute($request, $language);
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
