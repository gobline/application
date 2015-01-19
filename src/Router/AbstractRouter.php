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

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
abstract class AbstractRouter implements RouterInterface
{
    protected $defaultModule = 'index';

    public function setDefaultModule($defaultModule)
    {
        $defaultModule = (string) $defaultModule;
        if ($defaultModule === '') {
            throw new \InvalidArgumentException('$defaultModule cannot be empty');
        }

        $this->defaultModule = $defaultModule;
    }

    protected function makeKeyValuePairs(array $array)
    {
        $pairs = [];
        $nb = count($array);
        $i = 0;
        for (; $i < $nb - 1; $i += 2) {
            $pairs[$array[$i]] = $array[$i+1];
        }
        if ($i < $nb) {
            $pairs[$array[$i]] = '';
        }

        return $pairs;
    }

    protected function encodeParam($param)
    {
        return rawurlencode(str_replace('/', '%%', (string) $param));
    }

    protected function decodeParam($param)
    {
        return str_replace('%%', '/', rawurldecode($param));
    }

    protected function getSegments($path)
    {
        $path = trim($path, '/');

        if ($path === '') {
            return [];
        }

        return explode('/', $path);
    }
}
