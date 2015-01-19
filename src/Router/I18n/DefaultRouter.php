<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Router\I18n;

use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DefaultRouter extends AbstractRouter
{
    private $modules;
    private $modulesToTranslate;

    public function __construct(array $modules, array $modulesToTranslate = [])
    {
        $this->modules = $modules;
        $this->modulesToTranslate = $modulesToTranslate;
    }

    public function getName()
    {
        return 'defaultI18n';
    }

    public function match(HttpRequestInterface $httpRequest)
    {
        $language = $httpRequest->getLanguage();

        if (!$language || !$this->translator->hasTranslations($language)) {
            return false;
        }

        $segments = $this->getSegments($httpRequest->getPath());

        if (!$segments) {
            return false;
        }

        $mvcRequest = new MvcRequest($this->getName(), $this->defaultModule, 'index', 'index');

        $translations = array_flip($this->translator->getTranslations($language));

        $segment = array_shift($segments);
        $segment = isset($translations[$segment]) ? $translations[$segment] : $segment;

        if (in_array($segment, $this->modules)) {
            if ($this->modulesToTranslate && !in_array($segment, $this->modulesToTranslate)) {
                return false;
            }

            $mvcRequest->setModule($segment);

            if ($segments) {
                $segment = array_shift($segments);
                $segment = isset($translations[$segment]) ? $translations[$segment] : $segment;

                $mvcRequest->setController($segment);
            }
        } else {
            $mvcRequest->setController($segment);
        }

        if ($segments) {
            $segment = array_shift($segments);
            $segment = isset($translations[$segment]) ? $translations[$segment] : $segment;

            $mvcRequest->setAction($segment);
        }

        if ($segments) {
            $paramsTranslated = [];

            $params = $this->makeKeyValuePairs($segments);

            foreach ($params as $name => $value) {
                $name = isset($translations[$name]) ? $translations[$name] : $name;
                $paramsTranslated[$name] = $this->decodeParam($value);
            }

            $mvcRequest->setParams($paramsTranslated);
        }

        return $mvcRequest;
    }

    public function makeUrl(MvcRequest $request, $language = null)
    {
        $module = $this->translator->translate($request->getModule(), null, $language);
        $controller = $this->translator->translate($request->getController(), null, $language);
        $action = $this->translator->translate($request->getAction(), null, $language);
        $params = [];

        foreach ($request->getParams() as $name => $value) {
            $name = $this->translator->translate($name, null, $language);
            $params[$name] = $this->encodeParam($value);
        }

        $url = '';

        if ($module !== $this->defaultModule) {
            $url .= '/'.$module;
        }
        if ($action !== 'index' || $params) {
            $url .= '/'.$controller;
            $url .= '/'.$action;
            foreach ($params as $name => $value) {
                $value = $this->encodeParam($value);
                $url .= '/'.$name.'/'.$value;
            }
        } elseif ($controller !== 'index') {
            $url .= '/'.$controller;
        }

        return $url;
    }
}
