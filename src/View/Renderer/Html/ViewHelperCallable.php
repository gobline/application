<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Renderer\Html;

use Pimple\Container;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ViewHelperCallable
{
    private $getHelperClosure;

    public function __construct(callable $getHelperClosure)
    {
        $this->getHelperClosure = $getHelperClosure;
    }

    public function __invoke(...$arguments)
    {
        return $this->getHelper()->__invoke(...$arguments);
    }

    public function __call($name, array $arguments)
    {
        return $this->getHelper()->$name(...$arguments);
    }

    public function __get($name)
    {
        return $this->getHelper()->__get($name);
    }

    public function __toString()
    {
        return $this->getHelper()->__toString();
    }

    private function getHelper()
    {
        $getHelperClosure = $this->getHelperClosure;
        return $getHelperClosure();
    }
}
