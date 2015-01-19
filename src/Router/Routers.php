<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Router;

use IteratorAggregate;
use ArrayIterator;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Routers implements IteratorAggregate
{
    private $routers = [];
    private $defaultModule = 'index';

    public function add(RouterInterface $router)
    {
        $router->setDefaultModule($this->defaultModule);

        $this->routers = [$router->getName() => $router] + $this->routers;

        return $this;
    }

    public function has($name)
    {
        if ((string) $name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        return isset($this->routers[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \Exception('router "'.$name.'" not found');
        }

        return $this->routers[$name];
    }

    public function getIterator()
    {
        return new ArrayIterator($this->routers);
    }

    public function setDefaultModule($defaultModule)
    {
        $defaultModule = (string) $defaultModule;
        if ($defaultModule === '') {
            throw new \InvalidArgumentException('$defaultModule cannot be empty');
        }

        $this->defaultModule = $defaultModule;
    }
}
