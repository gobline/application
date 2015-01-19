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
class UrlMaker
{
    private $routers;
    private $httpRequest;

    public function __construct(Routers $routers, HttpRequestInterface $httpRequest)
    {
        $this->routers = $routers;
        $this->httpRequest = $httpRequest;
    }

    public function makeUrl(MvcRequest $request, $language = null)
    {
        if (!$language) {
            $language = $this->httpRequest->getLanguage();
        }

        $path = $this->routers->get($request->getRoute())->makeUrl($request, $language);

        $httpRequest = clone $this->httpRequest;
        $httpRequest->setPath($path);

        $makeAbsoluteUrl = false;

        if ($language) {
            $makeAbsoluteUrl = ($httpRequest->getLanguage() !== $language);
            $httpRequest->setLanguage($language);
        }

        return $httpRequest->getUrl($makeAbsoluteUrl);
    }
}
