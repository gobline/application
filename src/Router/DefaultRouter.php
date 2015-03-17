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
use Mendo\Router\AbstractRouter;
use Mendo\Router\RouteData;
use Mendo\Translator\TranslatorInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DefaultRouter extends AbstractRouter 
{
    private $router;

    public function __construct($name, $routePrefix, $defaults, TranslatorInterface $translator = null)
    {
        parent::__construct($name);

        $route = ($routePrefix ? $routePrefix : '(/)').'(/:controller(/)(/:action(/)(/:params+)))';
        $constraints = [
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]+',
            'action' => '[a-zA-Z_][a-zA-Z0-9_-]+',
        ];

        if ($translator) {
            $this->router = new PlaceholderI18nRouter($name, $route, $defaults, $constraints, ['controller', 'action', 'params']);
            $this->router->setTranslator($translator);
        } else {
            $this->router = new PlaceholderRouter($name, $route, $defaults, $constraints);
        }
    }

    public function match(HttpRequestInterface $httpRequest)
    {
        $routeData = $this->router->match($httpRequest);

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
        $params = [];
        foreach ($routeData->getParams() as $key => $value) {
            if (
                $key === 'module' || 
                $key === 'controller' || 
                $key === 'action'
            ) {
                $params[$key] = $value;
            } else {
                $params['params'][] = $key;
                $params['params'][] = $value;
            }
        }

        $routeData->setParams($params);

        return $this->router->makeUrl($routeData, $language, $absolute);
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
