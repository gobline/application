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
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class WhoopsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['whoops.error_page_handler'] = function () {
            return new PrettyPageHandler();
        };

        $container['whoops.mendo_info_handler'] = $container->protect(function () use ($container) {
            $errorPageHandler = $container['whoops.error_page_handler'];
            try {
                $httpRequest = $container['request.http'];
                $errorPageHandler->addDataTable('Mendo HttpRequest', [
                    'URL'          => $httpRequest->getUrl(true),
                    'Relative URL' => $httpRequest->getUrl(),
                    'Path'         => $httpRequest->getPath(),
                    'Base URL'     => $httpRequest->getBaseUrl(),
                    'Method'       => $httpRequest->getMethod(),
                    'Language'     => $httpRequest->getLanguage(),
                    'Default language' => $httpRequest->getDefaultLanguage(),
                    'isAjax'           => ($httpRequest->isAjax() ? 'true' : 'false'),
                    'isJsonRequest'    => ($httpRequest->isJsonRequest() ? 'true' : 'false'),
                ]);
            } catch (Exception $e) {
            }
            try {
                $mvcRequest = $container['request.mvc'];
                $errorPageHandler->addDataTable('Mendo MvcRequest', [
                    'Route'      => $mvcRequest->getRoute(),
                    'Module'     => $mvcRequest->getModule(),
                    'Controller' => $mvcRequest->getController(),
                    'Action'     => $mvcRequest->getAction(),
                    'Params'     => print_r($mvcRequest->getParams(), true),
                    'Forwarded'  => $mvcRequest->isForwarded(),
                    'Dispatched' => ($mvcRequest->isDispatched() ? 'true' : 'false'),
                ]);
            } catch (Exception $e) {
            }
            try {
                $auth = $container['auth'];
                $errorPageHandler->addDataTable('Mendo Auth', [
                    'Authenticated' => (bool) $auth->isAuthenticated(),
                    'Role'          => $auth->getRole(),
                    'Id'            => $auth->getId(),
                    'Login'         => $auth->getLogin(),
                    'Properties'    => $auth->getProperties(),
                ]);
            } catch (Exception $e) {
            }
        });

        $container['whoops'] = function ($c) {
            $run = new Run();
            $run->pushHandler($c['whoops.error_page_handler']);
            $run->pushHandler($c['whoops.mendo_info_handler']);

            return $run;
        };
    }
}
