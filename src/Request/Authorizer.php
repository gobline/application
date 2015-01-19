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

use Mendo\Auth\CurrentUserInterface;
use Mendo\Acl\AclInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Authorizer
{
    private $currentUser;
    private $acl;

    public function __construct(CurrentUserInterface $currentUser, AclInterface $acl)
    {
        $this->currentUser = $currentUser;
        $this->acl = $acl;
    }

    public function authorize(MvcRequest $request)
    {
        if ($this->isAuthorized($request)) {
            return true;
        }

        throw new Exception\NotAuthorizedException('Access denied to ' .(string) $request, 403);
    }

    public function isAuthorized(MvcRequest $request)
    {
        $action = $request->getModule().'/'.$request->getController().'/'.$request->getAction();

        $role = $this->currentUser->getRole();

        return $this->acl->hasRole($role) && $this->acl->isAllowed($role, $action);
    }
}
