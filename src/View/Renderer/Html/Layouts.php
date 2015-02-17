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

use Mendo\Mvc\Request\MvcRequest;
use IteratorAggregate;
use ArrayIterator;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Layouts implements IteratorAggregate
{
    private $routeLayoutFileMap = [];
    private $layouts = [];
    private $request;

    public function __construct(MvcRequest $request)
    {
        $this->request = $request;
    }

    public function add($route, $layouts)
    {
        if (!is_array($layouts)) {
            $layouts = [$layouts];
        }

        $this->routeLayoutFileMap[$route] = $layouts;

        return $this;

    }

    public function getLayouts()
    {
        if ($this->layouts) {
            return $this->layouts;
        }

        $bestMatch = null;
        $currentRoute = (string) $this->request;

        foreach ($this->routeLayoutFileMap as $route => $layouts) {
            if (
                $this->startsWith($currentRoute, $route) &&
                ($bestMatch === null || strlen($route) > strlen($bestMatch))
            ) {
                $bestMatch = $route;
            }
        }

        if ($bestMatch !== null) {
            $this->layouts = $this->routeLayoutFileMap[$bestMatch] ?: [];
        } else {
            $this->layouts = [$this->request->getModule()];
        }

        return $this->layouts;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->getLayouts());
    }

    private function startsWith($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}
