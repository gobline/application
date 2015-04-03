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
use Mendo\Mvc\Router\DefaultRouter;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DefaultRouterServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['router.mvc.defaultRouter.defaultModule'] = 'index';

        $container['router.mvc.defaultRouter'] = function ($c) {
            return new DefaultRouter($c['module.collection']->getRoutableModuleNames(), $c['router.mvc.defaultRouter.defaultModule']);
        };

        $container->extend('router.collection', function ($routers, $c) {
            if (!empty($c['router.mvc.defaultRouter'])) {
                $routers->add($c['router.mvc.defaultRouter']);
            }

            return $routers;
        });
    }
}
