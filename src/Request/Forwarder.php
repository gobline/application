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

use Mendo\Flash\FlashInterface;
use Mendo\Mvc\Request\Dispatcher;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Forwarder
{
    private $dispatcher;
    private $mvcRequest;

    public function __construct(Dispatcher $dispatcher, MvcRequest $mvcRequest)
    {
        $this->dispatcher = $dispatcher;
        $this->mvcRequest = $mvcRequest;
    }

    public function forward(MvcRequest $request)
    {
        $this->mvcRequest->copy($request);
        $this->mvcRequest->setForwarded();

        $this->dispatcher->dispatch();
    }
}
