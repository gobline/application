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
use Mendo\Mvc\Error\ErrorRedirector;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ErrorRedirectorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['error.redirector'] = function ($c) {
            return new ErrorRedirector(
                $c['request.mvc'],
                $c['request.authorizer'],
                $c['request.forwarder'],
                $c['request.redirector'],
                $c['modules']
            );
        };
    }
}
