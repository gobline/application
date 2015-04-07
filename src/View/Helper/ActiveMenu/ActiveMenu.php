<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\ActiveMenu;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ActiveMenu implements ViewHelperInterface
{
    private $mvcRequest;
    private $class;

    public function __construct(MvcRequest $mvcRequest, $class = 'active')
    {
        $this->mvcRequest = $mvcRequest;
        $this->class = $class;
    }

    public function template($template)
    {
        if (substr($template, -1) === '*') {
            if ($this->startsWith($this->mvcRequest->getTemplate(), rtrim($template, '*'))) {
                return $this->class;
            }

            return '';
        }

        if ($template !== $this->mvcRequest->getTemplate()) {
            return '';
        }

        return $this->class;
    }

    public function activeMenu($route)
    {
        $route = explode('/', $route);
        switch (count($route)) {
            case 3:
                $module = $route[0];
                $controller = $route[1];
                $action = $route[2];
                break;
            case 2:
                $module = $route[0];
                $controller = $route[1];
                $action = null;
                break;
            case 1:
                $module = $route[0];
                $controller = null;
                $action = null;
                break;
            default:
                throw new \InvalidArgumentException('$route invalid');
        }

        if ($module !== $this->mvcRequest->getModule()) {
            return '';
        }

        if (!$controller) {
            return $this->class;
        }

        if ($controller !== $this->mvcRequest->getController()) {
            return '';
        }

        if (!$action) {
            return $this->class;
        }

        if ($action !== $this->mvcRequest->getAction()) {
            return '';
        }

        return $this->class;
    }

    private function startsWith($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}
