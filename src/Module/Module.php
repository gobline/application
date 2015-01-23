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
class Module
{
    protected $name;
    protected $path;
    protected $namespace;
    protected $controllerPaths = [];
    protected $viewModelPaths = [];
    protected $templatePaths = [];
    protected $routable;

    public function __construct($name, $path, $namespace, $routable = true)
    {
        $this->name = (string) $name;
        if ($this->name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        $this->path = (string) $path;
        if ($this->path === '') {
            throw new \InvalidArgumentException('$path cannot be empty');
        }

        $this->namespace = (string) $namespace;
        if ($this->namespace === '') {
            throw new \InvalidArgumentException('$namespace cannot be empty');
        }

        $this->path = $this->trailingSlashIt($this->path);
        $this->namespace = $this->trailingSlashIt($this->namespace);

        $this->addControllerPath($this->namespace.'Controller', $this->path.'Controller/');
        $this->addViewModelPath($this->namespace.'ViewModel', $this->path.'ViewModel/');
        $this->addTemplatePath($this->path.'View/');

        $this->routable = $routable;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getControllerPaths()
    {
        return $this->controllerPaths;
    }

    public function getViewModelPaths()
    {
        return $this->viewModelPaths;
    }

    public function getTemplatePaths()
    {
        return $this->templatePaths;
    }

    public function addControllerPath($namespace, $path)
    {
        if ((string) $namespace === '') {
            throw new \InvalidArgumentException('$namespace cannot be empty');
        }

        if ((string) $path === '') {
            throw new \InvalidArgumentException('$path cannot be empty');
        }

        $namespace = $this->trailingSlashIt($namespace);
        $this->controllerPaths[$namespace] = $this->trailingSlashIt($path);
    }

    public function addViewModelPath($namespace, $path)
    {
        if ((string) $namespace === '') {
            throw new \InvalidArgumentException('$namespace cannot be empty');
        }

        if ((string) $path === '') {
            throw new \InvalidArgumentException('$path cannot be empty');
        }

        $namespace = $this->trailingSlashIt($namespace);
        $this->viewModelPaths[$namespace] = $this->trailingSlashIt($path);
    }

    public function addTemplatePath($path)
    {
        if ((string) $path === '') {
            throw new \InvalidArgumentException('$path cannot be empty');
        }

        $this->templatePaths[] = $this->trailingSlashIt($path);
    }

    public function isRoutable()
    {
        return $this->routable;
    }

    private function trailingSlashIt($s)
    {
        return strlen($s) <= 0 ? '/' : (substr($s, -1) !== '/' ? $s.'/' : $s);
    }
}
