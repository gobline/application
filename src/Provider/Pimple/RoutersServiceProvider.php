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
use Mendo\Mvc\Router\Routers;
use Mendo\Mvc\Router\DefaultRouter;
use Mendo\Mvc\Router\I18n\DefaultRouter as DefaultI18nRouter;
use Mendo\Translator\Translator;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class RoutersServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['router.default'] = function ($c) {
            return new DefaultRouter($c['modules']->getRoutableModuleNames());
        };

        $container['router.default.i18n.translations'] = [];
        $container['router.default.i18n.modules'] = [];

        $container['router.default.i18n'] = function ($c) {
            $translator = new Translator();
            foreach ($c['router.default.i18n.translations'] as $language => $files) {
                foreach ($files as $file) {
                    $translator->addTranslationFile($file, $language);
                }
            }
            $router = new DefaultI18nRouter($c['modules']->getRoutableModuleNames(), $c['router.default.i18n.modules']);
            $router->setTranslator($translator);

            return $router;
        };

        $container['routers.defaultModule'] = 'index';

        $container['routers'] = function ($c) {
            $routers = new Routers();
            $routers->setDefaultModule($c['routers.defaultModule']);
            $routers->add($c['router.default']);
            if ($c['router.default.i18n']) {
                $routers->add($c['router.default.i18n']);
            }

            return $routers;
        };
    }
}
