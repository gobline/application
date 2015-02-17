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
use Mendo\Mvc\View\Renderer\TemplateFileResolver;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class TemplateFileResolverServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['view.templateFileResolver'] = function ($c) {
            return new TemplateFileResolver($c['request.mvc'], $c['module.collection']);
        };
    }
}
