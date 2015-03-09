<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Identity;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Auth\CurrentUserInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Identity implements CurrentUserInterface, ActionHelperInterface
{
    private $user;

    public function __construct(CurrentUserInterface $user)
    {
        $this->user = $user;
    }

    public function identity()
    {
        return $this->user;
    }

    public function setId($id)
    {
        return $this->user->setId($id);
    }

    public function setLogin($login)
    {
        return $this->user->setLogin($login);
    }

    public function setRole($role)
    {
        return $this->user->setRole($role);
    }

    public function setProperties(array $properties)
    {
        return $this->user->setProperties($properties);
    }

    public function isAuthenticated()
    {
        return $this->user->isAuthenticated();
    }

    public function clearIdentity()
    {
        return $this->user->clearIdentity();
    }

    public function getId()
    {
        return $this->user->getId();
    }

    public function getLogin()
    {
        return $this->user->getLogin();
    }

    public function getRole()
    {
        return $this->user->getRole();
    }

    public function setRoleUnauthenticated($role)
    {
        return $this->user->setRoleUnauthenticated($role);
    }

    public function hasProperty($name)
    {
        return $this->user->hasProperty($name);
    }

    public function getProperty(...$args)
    {
        return $this->user->getProperty(...$args);
    }

    public function addProperty($name, $value)
    {
        return $this->user->addProperty($name, $value);
    }

    public function removeProperty($name)
    {
        return $this->user->removeProperty($name);
    }

    public function getProperties()
    {
        return $this->user->getProperties();
    }
}
