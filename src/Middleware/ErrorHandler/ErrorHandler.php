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

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ErrorHandler extends AbstractErrorHandler
{
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    protected function getCode()
    {
        return 500;
    }

    protected function getData()
    {
        return [
            'homeUri' => $this->environment->buildUri('/'),
        ];
    }
}
