<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc;

use Pimple\Container;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Bootstrap
{
    private $container;

    public function __construct(Container $c)
    {
        $this->container = $c;

        $errorHandler = $c['error.handler']; // set default error and exception handlers

        $eventDispatcher = $c['eventDispatcher.mvc'];
        $eventDispatcher->dispatch('start');

        $c['request.mvc'] = $c['router.mvc.requestMatcher']->match($c['request.http']);

        if (!empty($c['error.redirector'])) {
            $errorHandler->setErrorRedirector($c['error.redirector']);
        } else {
            $c['whoops']->register();
        }

        $eventDispatcher->dispatch('beforeDispatchLoop');

        $c['request.dispatcher']->dispatch();

        $eventDispatcher->dispatch('afterDispatchLoop');
    }

    public function __destruct()
    {
        $this->container['eventDispatcher.mvc']->dispatch('end');
    }
}
