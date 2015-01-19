<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Request;

use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Mvc\Router\Routers;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class MvcRequestFactory
{
    private $routers;

    public function __construct(Routers $routers)
    {
        $this->routers = $routers;
    }

    public function createRequest(HttpRequestInterface $httpRequest)
    {
        foreach ($this->routers as $route => $router) {
            $mvcRequest = $router->match($httpRequest);

            if ($mvcRequest) {
                return $mvcRequest;
            }
        }

        throw new \RuntimeException('No matching route for request "'.$httpRequest->getUrl(true).'"');
    }
}
