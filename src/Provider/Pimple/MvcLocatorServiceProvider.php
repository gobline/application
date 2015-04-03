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
use Mendo\Mvc\MvcLocator;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class MvcLocatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['mvcLocator'] = function ($c) {
            return new MvcLocator(
            	$c['module.collection'],
            	$c,
            	$c['action.helper.container']);
        };
    }
}
