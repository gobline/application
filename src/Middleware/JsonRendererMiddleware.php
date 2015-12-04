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
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class JsonRendererMiddleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $accept = $request->getHeaderLine('Accept');
        if (!$accept || !preg_match('#^application/([^+\s]+\+)?json#', $accept)) {
            return $next($request, $response);
        }

        $template = $request->getAttribute('_view', []);

        if (!isset($template['application/json'])) {
            return $next($request, $response);
        }

        $template = $template['application/json'];

        $model = $request->getAttribute('_model');

        $content = include $template;

        return new JsonResponse($content);
    }
}
