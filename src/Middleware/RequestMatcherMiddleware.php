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

use Gobline\Router\RequestMatcher;
use Gobline\Environment\Environment;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class RequestMatcherMiddleware
{
    private $requestMatcher;
    private $environment;

    public function __construct(RequestMatcher $requestMatcher, Environment $environment)
    {
        $this->requestMatcher = $requestMatcher;
        $this->environment = $environment;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $request = $request->withAttribute('_language', $this->environment->getLanguage());

        $requestToMatch = $this->environment->getBasePathResolver()->removeBasePath($request);
        $requestToMatch = $this->environment->getLanguageResolver()->removeLanguage($requestToMatch);

        $routeData = $this->requestMatcher->match($requestToMatch);

        $this->environment->setMatchedRouteName($routeData->getName());
        $this->environment->setMatchedRouteParams($routeData->getParams());

        foreach ($routeData->getParams() as $name => $value) {
            if (isset($request->getAttributes()[$name])) {
                continue;
            }

            $request = $request->withAttribute($name, $value);
        }

        $handler = $routeData->getHandler();
        $request = $request->withAttribute('_handler', $handler);

        return $next($request, $response);
    }
}
