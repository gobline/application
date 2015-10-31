<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Env;

use Mendo\Mvc\View\Helper\ViewHelperInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Env implements ViewHelperInterface
{
    private $env;

    public function __construct($env)
    {
        $this->env = (string) $env;
        if ($this->env === '') {
            throw new \InvalidArgumentException('$env cannot be empty');
        }
    }

    public function is($env)
    {
        return $env === $this->env;
    }

    public function __get($name)
    {
        return $this->is($name);
    }

    public function __toString()
    {
        return $this->env;
    }
}
