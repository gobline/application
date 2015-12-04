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
use Gobline\Auth\CurrentUserInterface;
use Gobline\Auth\Exception\NotAuthenticatedException;
use Gobline\Acl\Acl;
use Gobline\Acl\Exception\NotAuthorizedException;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class AuthorizerMiddleware
{
    private $currentUser;
    private $acl;

    public function __construct(CurrentUserInterface $currentUser, Acl $acl)
    {
        $this->currentUser = $currentUser;
        $this->acl = $acl;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $requiredRole = $request->getAttribute('_auth');

        if (!$requiredRole) {
            return $next($request, $response);
        }

        $userRole = $this->currentUser->getRole();

        if (
            !$this->acl->hasRole($requiredRole) ||
            !$this->acl->hasRole($userRole) ||
            (
                !$this->acl->getRole($userRole)->equals($requiredRole) &&
                !$this->acl->getRole($userRole)->inherits($requiredRole)
            )
        ) {
            if (!$this->currentUser->isAuthenticated()) {
                throw new NotAuthenticatedException();
            }

            throw new NotAuthorizedException();
        }

        return $next($request, $response);
    }
}
