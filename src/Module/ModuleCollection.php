<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Module;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ModuleCollection
{
    private $modules = [];
    private $routableModules = [];

    public function add(Module $module)
    {
        $name = $module->getName();

        $this->modules[$name] = $module;
        if ($module->isRoutable()) {
            $this->routableModules[$name] = $module;
        }

        return $module;
    }

    public function has($name)
    {
        if ((string) $name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        return isset($this->modules[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException('Module "'.$name.'" not found');
        }

        return $this->modules[$name];
    }

    public function getModuleNames()
    {
        return array_keys($this->modules);
    }

    public function getRoutableModuleNames()
    {
        return array_keys($this->routableModules);
    }

    private function trailingSlashIt($s)
    {
        return strlen($s) <= 0 ? '/' : (substr($s, -1) !== '/' ? $s.'/' : $s);
    }
}
