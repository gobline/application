<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Forward;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Mvc\Request\Dispatcher;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Forward implements ActionHelperInterface
{
    private $dispatcher;
    private $mvcRequest;

    public function __construct(Dispatcher $dispatcher, MvcRequest $mvcRequest)
    {
        $this->dispatcher = $dispatcher;
        $this->mvcRequest = $mvcRequest;
    }

    public function forward($action = null, $controller = null, $module = null, array $params = [])
    {
        if ($action) {
            $this->mvcRequest->setAction($action);
        }

        if ($controller) {
            $this->mvcRequest->setController($controller);
        }

        if ($module) {
            $this->mvcRequest->setModule($module);
        }

        $this->mvcRequest->setParams($params);
        $this->mvcRequest->setForwarded();
        $this->dispatcher->dispatch($this->mvcRequest);
    }
}
