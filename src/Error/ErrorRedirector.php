<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Error;

use Mendo\Mvc\Request\MvcRequest;
use Mendo\Mvc\Request\Authorizer;
use Mendo\Mvc\Request\Forwarder;
use Mendo\Mvc\Request\Redirector;
use Mendo\Mvc\Module\Modules;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ErrorRedirector
{
    private $request;
    private $authorizer;
    private $forwarder;
    private $redirector;
    private $modules;

    public function __construct(
        MvcRequest $mvcRequest,
        Authorizer $authorizer,
        Forwarder $forwarder,
        Redirector $redirector,
        Modules $modules
    ) {
        $this->request = $mvcRequest;
        $this->authorizer = $authorizer;
        $this->forwarder = $forwarder;
        $this->redirector = $redirector;
        $this->modules = $modules;
    }

    public function redirect(\Exception $e)
    {
        $code = ($e->getCode() === 0) ? 500 : $e->getCode();

        $request = new MvcRequest('default', $this->request->getModule(), 'error', '_'.$code);

        if (!$this->authorizer->isAuthorized($request)) {
            if (!headers_sent()) {
                header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden', true, 403);
            }

            echo "Request not authorized";
            exit;
        }

        if (!$this->request->isDispatched() || $code === 404) {
            $this->forwarder->forward($request);
            return;
        }

        if (!headers_sent()) {
            $this->redirector->redirect($request);
            exit;
        }

        echo "Request error";
        exit;
    }
}
