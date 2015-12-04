<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Application\Middleware\ErrorHandler;

use Gobline\Environment\Environment;
use Gobline\Auth\CurrentUserInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class NotAuthenticatedHandler extends AbstractErrorHandler
{
    private $currentUser;
    private $environment;

    public function __construct(Environment $environment, CurrentUserInterface $currentUser)
    {
        $this->environment = $environment;
        $this->currentUser = $currentUser;
    }

    protected function getCode()
    {
        return 401;
    }

    protected function getData()
    {
        $sessionExpired = false;

        if (
            $this->currentUser instanceof \Gobline\Auth\Persistence\Session &&
            $this->currentUser->isSessionExpired()
        ) {
            $sessionExpired = true;
        }

        return [
            'sessionExpired' => $sessionExpired,
            'homeUri' => $this->environment->buildUri('/'),
        ];
    }
}
