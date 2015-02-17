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
use Mendo\Mvc\Router\RequestMatcher;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class RequestMatcherServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['router.mvc.requestMatcher'] = function ($c) {
            return new RequestMatcher($c['router.requestMatcher']);
        };
    }
}
