<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Placeholder;

use Mendo\Mvc\View\Helper\ViewHelperInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Placeholder implements ViewHelperInterface
{
    private $container = [];

    public function __invoke(...$args)
    {
        return $this->get(...$args);
    }

    public function has($name)
    {
        if ((string) $name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        return array_key_exists($name, $this->container);
    }

    public function get(...$args)
    {
        switch (count($args)) {
            default:
                throw new \InvalidArgumentException('get() takes one or two arguments');
            case 1:
                if (!$this->has($args[0])) {
                    throw new \InvalidArgumentException('Placeholder "'.$args[0].'" not found');
                }
                return $this->container[$args[0]];
            case 2:
                if (!$this->has($args[0])) {
                    return $args[1];
                }
                return $this->container[$args[0]];
        }
    }

    public function set($name, $value)
    {
        if ((string) $name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        $this->container[$name] = $value;

        return $this;
    }

    public function __call($name, array $arguments)
    {
        if (empty($arguments)) {
            return $this->get($name);
        }
        $this->set($name, $arguments[0]);

        return $this;
    }
}
