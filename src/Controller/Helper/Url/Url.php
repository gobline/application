<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Url;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Mvc\Router\UrlMaker;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Url implements ActionHelperInterface
{
    private $urlMaker;
    private $request;

    public function __construct(UrlMaker $urlMaker, MvcRequest $request)
    {
        $this->urlMaker = $urlMaker;
        $this->request = $request;
    }

    public function url($action = null, $controller = null, $module = null, array $params = [], $language = null, $absolute = false)
    {
        if (!$action) {
            $action = $this->request->getAction();
        }

        if (!$controller) {
            $controller = $this->request->getController();
        }

        if (!$module) {
            $module = $this->request->getModule();
        }

        $request = new MvcRequest('default', $module, $controller, $action, $params);

        return $this->urlMaker->makeUrl($request, $language, $absolute);
    }

    public function route($route, array $data = [], $language = null, $absolute = false)
    {
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

        return $this->urlMaker->makeUrl($request, $language, $absolute);
    }

    public function self(array $data = [], $language = null, $absolute = false)
    {
        $module = $this->request->getModule();
        $controller = $this->request->getController();
        $action = $this->request->getAction();

        $request = new MvcRequest('default', $module, $controller, $action, $data);

        return $this->urlMaker->makeUrl($request, $language, $absolute);
    }

    public function template($template, $module = null, $language = null, $absolute = false)
    {
        $request = new MvcRequest('template', $module, null, null, [], $template);

        return $this->urlMaker->makeUrl($request, $language, $absolute);
    }
}
