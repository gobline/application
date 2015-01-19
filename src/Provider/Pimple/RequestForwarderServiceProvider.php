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
use Mendo\Mvc\Request\Forwarder;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class RequestForwarderServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['request.forwarder'] = function ($c) {
            return new Forwarder($c['request.dispatcher'], $c['request.mvc']);
        };
    }
}
