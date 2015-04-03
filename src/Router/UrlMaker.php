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

use Mendo\Router\UrlMakerInterface;
use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Router\RouteData;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class UrlMaker
{
    private $urlMaker;

    public function __construct(UrlMakerInterface $urlMaker)
    {
        $this->urlMaker = $urlMaker;
    }

    public function setContext(HttpRequestInterface $httpRequest = null)
    {
        $this->urlMaker->setContext($httpRequest);
    }

    public function getContext()
    {
        return $this->urlMaker->getContext();
    }

    public function makeUrl(MvcRequest $request, $language = null, $absolute = false)
    {
        $params = $request->getParams();
        $params['_module'] = $request->getModule();
        $params['_controller'] = $request->getController();
        $params['_action'] = $request->getAction();
        $params['_template'] = $request->getTemplate();

        return $this->urlMaker->makeUrl(new RouteData($request->getRoute(), $params), $language, $absolute);
    }
}
