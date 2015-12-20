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
use Gobline\Filter\FilterFunnel;
use Gobline\Router\Exception\InvalidParameterException;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class FilterMiddleware
{
    private $filter;

    public function __construct(FilterFunnel $filter)
    {
        $this->filter = $filter;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $parmetersRules = $request->getAttribute('_filter');

        if (!$parmetersRules) {
            return $next($request, $response);
        }

        foreach ($parmetersRules as $parameter => $rules) {
            $value = $request->getAttribute($parameter);

            if (!$this->filter->filter($value, $rules)) {
                throw new InvalidParameterException(
                    'parameter "'.$parameter.'" has an invalid value of "'.$value.'"');
            }
        }

        return $next($request, $response);
    }
}
