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
use Mendo\Mvc\Request\Hmvc;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class HmvcRequestServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['request.hmvc'] = function ($c) {
            return new Hmvc(
                $c['request.http'],
                $c['request.mvc'],
                $c['request.dispatcher'],
                $c['router.mvc.urlMaker']);
        };
    }
}
