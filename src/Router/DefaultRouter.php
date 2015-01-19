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
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DefaultRouter extends AbstractRouter
{
    private $modules;

    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    public function getName()
    {
        return 'default';
    }

    public function match(HttpRequestInterface $httpRequest)
    {
        $mvcRequest = new MvcRequest($this->getName(), $this->defaultModule, 'index', 'index');

        $segments = $this->getSegments($httpRequest->getPath());

        if (!$segments) {
            return $mvcRequest;
        }

        $segment = array_shift($segments);

        if (in_array($segment, $this->modules)) {
            $mvcRequest->setModule($segment);

            if ($segments) {
                $mvcRequest->setController(array_shift($segments));
            }
        } else {
            $mvcRequest->setController($segment);
        }

        if ($segments) {
            $mvcRequest->setAction(array_shift($segments));
        }

        if ($segments) {
            $params = $this->makeKeyValuePairs($segments);
            foreach ($params as $name => $value) {
                $params[$name] = $this->decodeParam($value);
            }
            $mvcRequest->setParams($params);
        }

        return $mvcRequest;
    }

    public function makeUrl(MvcRequest $request, $language = null)
    {
        $url = '';

        if ($request->getModule() !== $this->defaultModule) {
            $url .= '/'.$request->getModule();
        }

        if ($request->getAction() !== 'index' || $request->getParams()) {
            $url .= '/'.$request->getController();
            $url .= '/'.$request->getAction();
    
            foreach ($request->getParams() as $name => $value) {
                $value = $this->encodeParam($value);
                $url .= '/'.$name.'/'.$value;
            }
        } elseif ($request->getController() !== 'index') {
            $url .= '/'.$request->getController();
        }

        return $url;
    }
}
