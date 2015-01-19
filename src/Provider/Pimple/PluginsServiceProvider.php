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
use Mendo\Mvc\Plugin\SetHttpResponseErrorCode\SetHttpResponseErrorCode;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class PluginsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['eventDispatcher.mvc']
            ->addListener(
            	function() use ($container) {
            		return new SetHttpResponseErrorCode($container['request.mvc']);
            	}, 
            	['beforeDispatch' => 'beforeDispatch']);
    }
}
