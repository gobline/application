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
use Mendo\Mvc\Controller\Helper\BaseUrl\BaseUrl;
use Mendo\Mvc\Controller\Helper\Env\Env;
use Mendo\Mvc\Controller\Helper\Filter\Filter;
use Mendo\Mvc\Controller\Helper\Find\Find;
use Mendo\Mvc\Controller\Helper\Flash\Flash;
use Mendo\Mvc\Controller\Helper\Forward\Forward;
use Mendo\Mvc\Controller\Helper\Lang\Lang;
use Mendo\Mvc\Controller\Helper\Redirect\Redirect;
use Mendo\Mvc\Controller\Helper\Request\Request;
use Mendo\Mvc\Controller\Helper\Translate\Translate;
use Mendo\Mvc\Controller\Helper\Url\Url;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ActionHelperContainerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['action.helper.container'] = function ($c) {
            $helpers = new Container();

            $helpers['baseUrl'] = function () use ($c) {
                return new BaseUrl($c['request.http']);
            };

            $helpers['env'] = function () use ($c) {
                return new Env($c['environment']);
            };

            $helpers['filter'] = function () use ($c) {
                return new Filter($c['filterFunnelFactory']);
            };

            $helpers['find'] = function () use ($c) {
                return new Find($c['request.mvc'], $c['request.http'], $c['flash']);
            };

            $helpers['flash'] = function () use ($c) {
                return new Flash($c['flash']);
            };

            $helpers['forward'] = function () use ($c) {
                return new Forward($c['request.dispatcher'], $c['request.mvc']);
            };

            $helpers['lang'] = function () use ($c) {
                return new Lang($c['request.http']);
            };

            $helpers['redirect'] = function () use ($c) {
                return new Redirect($c['request.redirector'], $c['request.mvc']);
            };

            $helpers['request'] = function () use ($c) {
                return new Request($c['request.mvc'], $c['request.http']);
            };

            $helpers['translate'] = function () use ($c) {
                return new Translate($c['translator']);
            };

            $helpers['url'] = function () use ($c) {
                return new Url($c['router.urlMaker'], $c['request.mvc']);
            };

            return $helpers;
        };
    }
}
