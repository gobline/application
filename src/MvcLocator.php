<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc;

use Pimple\Container;
use Mendo\Mvc\Module\Modules;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Mvc\ViewModel\DefaultViewModel;
use Mendo\Mvc\Controller\DefaultController;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class MvcLocator
{
    private $modules;
    private $container;
    private $actionHelperContainer;
    private $viewHelperContainer;

    public function __construct(
        Modules $modules,
        Container $container,
        Container $actionHelperContainer,
        Container $viewHelperContainer
    ) {
        $this->modules = $modules;
        $this->container = $container;
        $this->actionHelperContainer = $actionHelperContainer;
        $this->viewHelperContainer = $viewHelperContainer;
    }

    public function getController(MvcRequest $request)
    {
        $className = $request->getController(true).'Controller';
        $paths = $this->modules->get($request->getModule())->getControllerPaths();
        $instance = $this->getInstance($request->getModule(true), $className, $paths);

        if ($instance === null) {
            $instance = new DefaultController();
        }

        $instance->setHelperContainer($this->actionHelperContainer);

        return $instance;
    }

    public function getViewModel(MvcRequest $request)
    {
        $className = $request->getController(true).'ViewModel';
        $paths = $this->modules->get($request->getModule())->getViewModelPaths();
        $instance = $this->getInstance($request->getModule(true), $className, $paths);

        if ($instance === null) {
            $instance = new DefaultViewModel();
        }

        if (!$instance->getTemplate()) {
            $instance->setTemplate($request->getController());
        }

        return $instance;
    }

    private function getInstance($moduleName, $className, array $paths)
    {
        if (!isset($this->container[$moduleName.'.'.$className])) {
            $namespaceTarget = null;

            foreach ($paths as $namespace => $path) {
                if (is_file($path.$className.'.php')) {
                    $namespaceTarget = $namespace;
                    break;
                }
            }

            if ($namespaceTarget === null) {
                return;
            }

            $class = str_replace('/', '\\', $namespaceTarget).$className;
            $class = new $class();

            $this->container[$moduleName.'.'.$className] =
                function () use ($class) {
                    return $class;
                };
        }

        return $this->container[$moduleName.'.'.$className];
    }
}
