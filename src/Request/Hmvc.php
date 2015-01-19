<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Request;

use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Mvc\Router\Routers;
use Mendo\Mvc\View\Renderer\ViewRendererInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Hmvc
{
    private $httpRequest;
    private $mvcRequest;
    private $dispatcher;
    private $routers;
    private $viewRenderer;

    public function __construct(
        HttpRequestInterface $httpRequest,
        MvcRequest $mvcRequest,
        Dispatcher $dispatcher,
        Routers $routers,
        ViewRendererInterface $viewRenderer
    ) {
        $this->httpRequest = $httpRequest;
        $this->mvcRequest = $mvcRequest;
        $this->dispatcher = $dispatcher;
        $this->routers = $routers;
        $this->viewRenderer = $viewRenderer;
    }

    public function execute(MvcRequest $subRequest, $language = null)
    {
        $post = $_POST;
        $_POST = [];

        $get = $_GET;
        $_GET = [];

        $originalHttpRequest = clone $this->httpRequest;

        $originalMvcRequest = clone $this->mvcRequest;

        if (!$language) {
            $language = $this->httpRequest->getLanguage();
        } else {
            $language = (string) $language;
            $this->httpRequest->setLanguage($language);
        }

        $path = $this->routers->get($subRequest->getRoute())->makeUrl($subRequest, $language);
        $this->httpRequest->setPath($path);
        $this->httpRequest->setQueryData([]);
        $this->httpRequest->setPostData([]);

        $this->mvcRequest->copy($subRequest);
        $this->mvcRequest->setSubRequest();

        ob_start();

        $this->dispatcher->dispatch($this->viewRenderer);

        $return = ob_get_clean();

        $_POST = $post;
        $_GET = $get;

        if ($originalHttpRequest->getLanguage()) {
            $this->httpRequest->setLanguage($originalHttpRequest->getLanguage());
        }
        $this->httpRequest->setPath($originalHttpRequest->getPath());
        $this->httpRequest->setQueryData($originalHttpRequest->getQuery());
        $this->httpRequest->setPostData($originalHttpRequest->getPost());

        $this->mvcRequest->copy($originalMvcRequest);

        return $return;
    }
}
