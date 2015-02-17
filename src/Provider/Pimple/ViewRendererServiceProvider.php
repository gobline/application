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
use Mendo\Mvc\View\Renderer\ViewRenderer;
use Mendo\Mvc\View\Renderer\Html\HtmlMasterLayoutRenderer;
use Mendo\Mvc\View\Renderer\Html\HtmlLayoutRenderer;
use Mendo\Mvc\View\Renderer\Html\HtmlTemplateRenderer;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ViewRendererServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['view.renderer'] = function ($c) {
            $renderer = new ViewRenderer($c['request.http'], $c['request.mvc']);

            if (!empty($c['view.renderer.html.masterLayoutRenderer'])) {
                $renderer->addRenderer($c['view.renderer.html.masterLayoutRenderer']);
            }

            if (!empty($c['view.renderer.html.layoutRenderer'])) {
                $renderer->addRenderer($c['view.renderer.html.layoutRenderer']);
            }

            if (!empty($c['view.renderer.html.templateRenderer'])) {
                $renderer->addRenderer($c['view.renderer.html.templateRenderer']);
            }

            return $renderer;
        };

        $container['view.renderer.html.masterLayoutRenderer'] = function ($c) {
            $renderer = empty($c['view.renderer.html.layoutRenderer']) ?
                $c['view.renderer.html.templateRenderer'] :
                $c['view.renderer.html.layoutRenderer'];

            return new HtmlMasterLayoutRenderer($renderer, $c['eventDispatcher.view']);
        };

        $container['view.renderer.html.layoutRenderer'] = function ($c) {
            return new HtmlLayoutRenderer(
                $c['view.renderer.html.templateRenderer'],
                $c['view.templateFileResolver'],
                $c['layouts']);
        };

        $container['view.renderer.html.templateRenderer'] = function ($c) {
            return new HtmlTemplateRenderer($c['view.helper.container'], $c['view.templateFileResolver']);
        };
    }
}
