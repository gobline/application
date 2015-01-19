<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Provider\Pimple;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Mendo\Mvc\Request\Dispatcher;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class RequestDispatcherServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['request.dispatcher.mapRouteParamsToActionArguments'] = true;

        $container['request.dispatcher'] = function ($c) {
            return new Dispatcher(
                $c['request.mvc'],
                $c['request.authorizer'],
                $c['mvcLocator'],
                $c['view.renderer'],
                $c['eventDispatcher.mvc'],
                $c['request.dispatcher.mapRouteParamsToActionArguments']);
        };
    }
}
