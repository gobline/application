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

    public function url($action = null, $controller = null, $module = null, array $params = [], $language = null)
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

        return $this->urlMaker->makeUrl($request, $language);
    }

    public function route($route, array $data = [], $language = null)
    {
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

        return $this->urlMaker->makeUrl($request, $language);
    }

    public function self(array $data = [], $language = null)
    {
        $module = $this->request->getModule();
        $controller = $this->request->getController();
        $action = $this->request->getAction();

        $request = new MvcRequest('default', $module, $controller, $action, $data);

        return $this->urlMaker->makeUrl($request, $language);
    }
}
