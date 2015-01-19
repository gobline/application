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
use Mendo\Mvc\View\Renderer\Html\Layouts;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class LayoutsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['view.renderer.html.layouts.routeMap'] = [];

        $container['view.renderer.html.layouts'] = function ($c) {
            return new Layouts(
                $c['request.mvc'],
                $c['view.renderer.html.layouts.routeMap']);
        };
    }
}
