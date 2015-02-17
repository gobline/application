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

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Bootstrap
{
    private $env;
    private $container;

    public function __construct($environment = null)
    {
        $environment = (string) $environment;
        $this->env = $environment ?: ($this->isLocalHost() ? 'dev' : 'prod');

        $this->init();

        $this->createContainer();
    }

    private function isLocalHost()
    {
        return $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1';
    }

    private function init()
    {
        if (is_file(getcwd().'/config/init.base.php')) {
            include getcwd().'/config/init.base.php';
        }

        if (is_file(getcwd().'/config/init.'.$this->env.'.php')) {
            include getcwd().'/config/init.'.$this->env.'.php';
        }
    }

    private function createContainer()
    {
        $this->container = new CompositionRoot(['environment' => $this->env]);
    }

    public function registerServices($file)
    {
        $services = [];

        if (is_file(getcwd().'/config/'.$file.'.base.php')) {
            $services = array_merge($services, include getcwd().'/config/'.$file.'.base.php');
        }

        if (is_file(getcwd().'/config/'.$file.'.'.$this->env.'.php')) {
            $services = array_merge($services, include getcwd().'/config/'.$file.'.'.$this->env.'.php');
        }

        foreach ($services as $service => $config) {
            if (empty($config['serviceProvider'])) {
                throw new \Exception('Service provider not specified');
            }

            $dependencies = isset($config['dependencies']) ? $config['dependencies'] : [];
            $parameters = isset($config['parameters']) ? $config['parameters'] : [];

            $parameters = array_merge($dependencies, $parameters);
            $serviceParameters = [];

            foreach ($parameters as $key => $value) {
                $serviceParameters[$service.'.'.$key] = $value;
            }

            $serviceProvider = $config['serviceProvider'];
            $this->container->register(new $serviceProvider($service), $serviceParameters);
        }

        return $this;
    }

    public function run()
    {
        $c = $this->container;

        $errorHandler = $c['error.handler']; // set default error and exception handlers

        $eventDispatcher = $c['eventDispatcher.mvc'];
        $eventDispatcher->dispatch('start');

        $c['request.mvc'] = $c['router.mvc.requestMatcher']->match($c['request.http']);

        if (!empty($c['error.redirector'])) {
            $errorHandler->setErrorRedirector($c['error.redirector']);
        } else {
            $c['whoops']->register();
        }

        $eventDispatcher->dispatch('beforeDispatchLoop');

        $c['request.dispatcher']->dispatch();

        $eventDispatcher->dispatch('afterDispatchLoop');
    }

    public function __destruct()
    {
        $this->container['eventDispatcher.mvc']->dispatch('end');
    }
}
