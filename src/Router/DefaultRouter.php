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

use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Router\PlaceholderRouter;
use Mendo\Router\I18n\PlaceholderRouter as PlaceholderI18nRouter;
use Mendo\Router\I18n\TranslatorAwareTrait;
use Mendo\Router\AbstractRouter;
use Mendo\Router\RouteData;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DefaultRouter extends AbstractRouter
{
    use TranslatorAwareTrait;

    private $modules;
    private $defaultModule;

    public function __construct(array $modules, $defaultModule = 'index')
    {
        parent::__construct('default');
        $this->modules = $modules;
        $this->defaultModule = $defaultModule;
    }

    private function createRouter($route)
    {
        $defaults = [
            '_module' => $this->defaultModule,
            '_controller' => 'index',
            '_action' => 'index',
        ];
        $constraints = [
            '_controller' => '[a-zA-Z][a-zA-Z0-9_-]+',
            '_action' => '[a-zA-Z_][a-zA-Z0-9_-]+',
        ];

        if ($this->translator) {
            $router = new PlaceholderI18nRouter($this->name, $route, $defaults, $constraints, ['_module', '_controller', '_action', 'params']);
            $router->setTranslator($this->translator);
        } else {
            $router = new PlaceholderRouter($this->name, $route, $defaults, $constraints);
        }

        return $router;
    }

    public function match(HttpRequestInterface $httpRequest)
    {
        $segments = explode('/', ltrim($httpRequest->getPath(), '/'));
        $isDefaultModule = true;
        if ($segments) {
            $language = $httpRequest->getLanguage();
            if ($this->translator && $language && $this->translator->hasTranslations($language)) {
                $segments[0] = array_search($segments[0], $this->translator->getTranslations($language)) ?: $segments[0];
            }
            $isDefaultModule = (!in_array($segments[0], $this->modules) || $segments[0] === $this->defaultModule);
        }

        $route = ($isDefaultModule) ? 
            '(/)(/:_controller(/)(/:_action(/)(/:params+)))' : 
            '(/:_module(/)(/:_controller(/)(/:_action(/)(/:params+))))';
        $router = $this->createRouter($route);

        $routeData = $router->match($httpRequest);

        if ($routeData === false) {
            return false;
        }

        if ($routeData->hasParam('params')) {
            $params = $routeData->getParam('params');
            $params = $this->makeKeyValuePairs($params);
            $params = $params + $routeData->getParams();
            unset($params['params']);
            $routeData->setParams($params);
        }

        return $routeData;
    }

    public function makeUrl(RouteData $routeData, $language = null, $absolute = false)
    {
        $route = ($routeData->getParam('_module', $this->defaultModule) === $this->defaultModule) ? 
            '(/)(/:_controller(/)(/:_action(/)(/:params+)))' : 
            '(/:_module(/)(/:_controller(/)(/:_action(/)(/:params+))))';
        $router = $this->createRouter($route);

        $params = [];
        foreach ($routeData->getParams() as $key => $value) {
            if (
                $key === '_template'
            ) {
                continue;
            }
            if (
                $key === '_module' ||
                $key === '_controller' ||
                $key === '_action'
            ) {
                $params[$key] = $value;
                continue;
            }

            $params['params'][] = $key;
            $params['params'][] = $value;
        }

        $routeData->setParams($params);

        return $router->makeUrl($routeData, $language, $absolute);
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
