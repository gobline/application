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
use Zend\Escaper\Escaper as ZendEscaper;
use Mendo\Mvc\View\Helper\ActiveMenuClass\ActiveMenuClass;
use Mendo\Mvc\View\Helper\Asset\AssetVersions;
use Mendo\Mvc\View\Helper\Asset\Collection\CssCollectionFactory;
use Mendo\Mvc\View\Helper\Asset\Collection\JsCollectionFactory;
use Mendo\Mvc\View\Helper\Asset\Css\Css;
use Mendo\Mvc\View\Helper\Asset\Js\Js;
use Mendo\Mvc\View\Helper\Asset\Minifier;
use Mendo\Mvc\View\Helper\Asset\ModuleAssetCopier;
use Mendo\Mvc\View\Helper\BaseUrl\BaseUrl;
use Mendo\Mvc\View\Helper\Description\Description;
use Mendo\Mvc\View\Helper\Env\Env;
use Mendo\Mvc\View\Helper\Escape\Escape;
use Mendo\Mvc\View\Helper\Find\Find;
use Mendo\Mvc\View\Helper\Form\Form;
use Mendo\Mvc\View\Helper\Hreflang\Hreflang;
use Mendo\Mvc\View\Helper\Lang\Lang;
use Mendo\Mvc\View\Helper\NoIndex\NoIndex;
use Mendo\Mvc\View\Helper\Placeholder\Placeholder;
use Mendo\Mvc\View\Helper\Request\Request;
use Mendo\Mvc\View\Helper\Responsive\Responsive;
use Mendo\Mvc\View\Helper\Title\Title;
use Mendo\Mvc\View\Helper\Translate\Translate;
use Mendo\Mvc\View\Helper\Url\Url;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ViewHelperContainerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['view.helper.activeMenuClass.class'] = 'active';
        $container['view.helper.css.minifier'] = function ($c) {
            return new Minifier();
        };
        $container['view.helper.js.minifier'] = function ($c) {
            return new Minifier();
        };
        $container['view.helper.assets'] = [];

        $container['view.helper.container'] = function ($c) {
            $helpers = new Container();

            $helpers['activeMenuClass'] = function () use ($c) {
                return new ActiveMenuClass($c['request.mvc'], $c['view.helper.activeMenuClass.class']);
            };

            $c['assetVersions'] = function ($c) {
                return new AssetVersions($c['view.helper.assets']);
            };

            $helpers['css'] = $helpers->factory(function () use ($c) {
                return new Css(
                    $c['eventDispatcher.view'],
                    $c['assetVersions'],
                    $c['view.helper.css.minifier'],
                    new ModuleAssetCopier($c['module.collection'], $c['request.mvc']),
                    $c['request.http']->getBaseUrl());
            });

            $helpers['js'] = $helpers->factory(function () use ($c) {
                return new Js(
                    $c['eventDispatcher.view'],
                    $c['assetVersions'],
                    $c['view.helper.js.minifier'],
                    new ModuleAssetCopier($c['module.collection'], $c['request.mvc']),
                    $c['request.http']->getBaseUrl());
            });

            $helpers['cssCollection'] = $helpers->factory(function () use ($c) {
                return new CssCollectionFactory(
                    $c['eventDispatcher.view'],
                    $c['assetVersions'],
                    $c['view.helper.css.minifier'],
                    new ModuleAssetCopier($c['module.collection'], $c['request.mvc']),
                    $c['request.http']->getBaseUrl());
            });

            $helpers['jsCollection'] = $helpers->factory(function () use ($c) {
                return new JsCollectionFactory(
                    $c['eventDispatcher.view'],
                    $c['assetVersions'],
                    $c['view.helper.js.minifier'],
                    new ModuleAssetCopier($c['module.collection'], $c['request.mvc']),
                    $c['request.http']->getBaseUrl());
            });

            $helpers['baseUrl'] = function () use ($c) {
                return new BaseUrl($c['request.http']);
            };

            $helpers['description'] = function () use ($c) {
                return new Description($c['eventDispatcher.view']);
            };

            $helpers['env'] = function () use ($c) {
                return new Env($c['environment']);
            };

            $helpers['escape'] = function () use ($c) {
                return new Escape(new ZendEscaper());
            };

            $helpers['find'] = function () use ($c) {
                return new Find($c['request.mvc'], $c['request.http'], $c['flash']);
            };

            $helpers['form'] = function () use ($c) {
                if (empty($c['view.helper.form.translate'])) {
                    return new Form();
                }

                return new Form($c['translator'], $c['request.http']);
            };

            $helpers['hreflang'] = function () use ($c) {
                return new Hreflang(
                    $c['eventDispatcher.view'],
                    $c['router.mvc.urlMaker'],
                    $c['request.mvc'],
                    $c['request.http.language.list']);
            };

            $helpers['lang'] = function () use ($c) {
                return new Lang($c['eventDispatcher.view'], $c['request.http']);
            };

            $helpers['noIndex'] = function () use ($c) {
                return new NoIndex($c['eventDispatcher.view']);
            };

            $helpers['placeholder'] = function () use ($c) {
                return new Placeholder();
            };

            $helpers['request'] = function () use ($c) {
                return new Request($c['request.hmvc'], $c['request.mvc']);
            };

            $helpers['responsive'] = function () use ($c) {
                return new Responsive($c['eventDispatcher.view']);
            };

            $helpers['title'] = function () use ($c) {
                return new Title($c['eventDispatcher.view']);
            };

            $helpers['translate'] = function () use ($c) {
                return new Translate($c['translator']);
            };

            $helpers['url'] = function () use ($c) {
                return new Url($c['router.mvc.urlMaker'], $c['request.mvc']);
            };

            return $helpers;
        };
    }
}
