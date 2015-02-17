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
use Mendo\Mvc\Request\Redirector;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class RequestRedirectorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['request.redirector'] = function ($c) {
            return new Redirector($c['router.mvc.urlMaker'], $c['flash']);
        };
    }
}
