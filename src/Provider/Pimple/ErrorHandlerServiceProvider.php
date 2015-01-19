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
use Mendo\Mvc\Error\ErrorHandler;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ErrorHandlerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['error.handler.logger'] = 'logger';
        $container['error.handler.logLevel.default'] = 'error';
        $container['error.handler.logLevel'] = [
            500 => 'error',
            404 => 'info',
            403 => 'info',
            401 => 'info',
        ];

        $container['error.handler'] = function ($c) {
            $errorHandler = new ErrorHandler();
            $errorHandler->setDefaultLogLevel($c['error.handler.logLevel.default']);

            foreach ($c['error.handler.logLevel'] as $code => $level) {
                $errorHandler->setLogLevel($code, $level);
            }

            if (!empty($c[$c['error.handler.logger']])) {
                $errorHandler->setLogger($c[$c['error.handler.logger']]);
            }

            return $errorHandler;
        };
    }
}
