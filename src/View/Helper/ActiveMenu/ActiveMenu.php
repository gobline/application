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
use Mendo\Http\Request\HttpRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ActiveMenu implements ViewHelperInterface
{
    private $mvcRequest;
    private $httpRequest;
    private $class;

    public function __construct(MvcRequest $mvcRequest, HttpRequest $httpRequest, $class = 'active')
    {
        $this->mvcRequest = $mvcRequest;
        $this->httpRequest = $httpRequest;
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
        $queryData = [];
        if (strpos($route, '?') !== false) {
            list($route, $queryString) = explode('?', $route);
            $parts = parse_url('?'.$queryString);
            if (array_key_exists('query', $parts)) {
                parse_str($parts['query'], $queryData);
            }
        }

        $route = explode('/', $route);

        $module = null;
        $controller = null;
        $action = null;
        $params = [];

        switch (count($route)) {
            default: 
                $params = array_slice($route, 3);
                $params = $this->makeKeyValuePairs($params);
            case 3:
                $action = $route[2];
            case 2:
                $controller = $route[1];
            case 1:
                $module = $route[0];
                break;
            case 0:
                throw new \InvalidArgumentException('$route invalid');
        }

        if ($queryData && array_diff_assoc($queryData, $this->httpRequest->getQuery())) {
            return '';
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

        if (!$params) {
            return $this->class;
        }

        if (array_diff_assoc($params, $this->mvcRequest->getParams())) {
            return '';
        }

        return $this->class;
    }

    private function startsWith($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }

    private function makeKeyValuePairs(array $array)
    {
        $pairs = [];
        $nb = count($array);
        for ($i = 0; $i < $nb - 1; $i += 2) {
            $pairs[$array[$i]] = $array[$i+1];
        }
        if ($i < $nb) {
            $pairs[$array[$i]] = '';
        }

        return $pairs;
    }
}
