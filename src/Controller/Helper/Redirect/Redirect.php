<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Redirect;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Mvc\Request\Redirector;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Redirect implements ActionHelperInterface
{
    private $redirector;
    private $request;

    public function __construct(Redirector $redirector, MvcRequest $request)
    {
        $this->redirector = $redirector;
        $this->request = $request;
    }

    public function redirect($action = null, $controller = null, $module = null, array $params = [], array $flashMessages = [], $language = null)
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

        $this->redirector->redirect($request, $flashMessages, $language);
        exit;
    }

    public function route($route, array $data = [], array $flashMessages = [], $language = null)
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

        $this->redirector->redirect($request, $flashMessages, $language);
        exit;
    }

    public function template($template, $module = null, array $flashMessages = [], $language = null)
    {
        $request = new MvcRequest('template', $module, null, null, [], $template);

        $this->redirector->redirect($request, $flashMessages, $language);
        exit;
    }
}
