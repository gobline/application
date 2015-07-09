<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Flash;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Flash\FlashInterface;
use \IteratorAggregate;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Flash implements FlashInterface, ActionHelperInterface, IteratorAggregate
{
    private $flash;

    public function __construct(FlashInterface $flash)
    {
        $this->flash = $flash;
    }

    public function flash($name, $value)
    {
        $this->flash->next($name, $value);
    }

    public function now($name, $value)
    {
        $this->flash->now($name, $value);
    }

    public function next($name, $value)
    {
        $this->flash->next($name, $value);
    }

    public function keep()
    {
        $this->flash->keep();
    }

    public function has($name)
    {
        return $this->flash->has($name);
    }

    public function get(...$args)
    {
        return $this->flash->get(...$args);
    }

    public function getIterator()
    {
        return $this->flash->getIterator();
    }

    public function getArrayCopy()
    {
        return $this->flash->getArrayCopy();
    }

    public function count()
    {
        $this->flash->count();
    }
}
