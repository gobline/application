<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Env;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Env implements ActionHelperInterface
{
    private $env;

    public function __construct($env)
    {
        $this->env = (string) $env;
        if ($this->env === '') {
            throw new \InvalidArgumentException('$env cannot be empty');
        }
    }

    public function env($env = null)
    {
        if ($env) {
            return $env === $this->env;
        }

        return $this->env;
    }

    public function __toString()
    {
        return $this->env;
    }
}
