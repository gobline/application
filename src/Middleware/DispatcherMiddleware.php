<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Application\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Gobline\Container\ContainerInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DispatcherMiddleware
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $handler = $request->getAttribute('_handler');

        if ($handler) {
            $response = $handler($request, $response, ...$this->getAttributes($request));
        }

        $actionModel = $request->getAttribute('_action');

        if (!$actionModel) {
            return $next($request, $response);
        }

        if (is_string($actionModel)) {
            $actionModel = $this->container->get($actionModel);
        }

        if (is_callable($actionModel)) {
            $actionModel($request, ...$this->getAttributes($request));
        }

        $request = $request->withAttribute('_model', $actionModel);

        return $next($request, $response);
    }

    private function getAttributes(ServerRequestInterface $request)
    {
        return array_values(array_filter($request->getAttributes(),
            function ($parameter) {
                return $parameter[0] !== '_';
            }, ARRAY_FILTER_USE_KEY));
    }
}
