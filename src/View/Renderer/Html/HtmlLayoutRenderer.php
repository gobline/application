<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Renderer\Html;

use Mendo\Mvc\ViewModel\AbstractViewModel;
use Mendo\Mvc\View\Renderer\ViewRendererInterface;
use Mendo\Mvc\View\Renderer\ViewRendererMatcherInterface;
use Mendo\Mvc\View\Renderer\TemplateFileResolver;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class HtmlLayoutRenderer implements ViewRendererMatcherInterface
{
    private $htmlRenderer;
    private $templateResolver;
    private $layouts;
    private $model;

    public function __construct(
        ViewRendererInterface $htmlRenderer,
        TemplateFileResolver $templateResolver,
        Layouts $layouts
    ) {
        $this->htmlRenderer = $htmlRenderer;
        $this->templateResolver = $templateResolver;
        $this->layouts = $layouts->getIterator();
    }

    public function __get($name)
    {
        return $this->htmlRenderer->$name;
    }

    public function __call($method, array $arguments)
    {
        return call_user_func_array([$this->htmlRenderer, $method], $arguments);
    }

    public function content()
    {
        ob_start();
        try {
            if ($this->layouts->current()) {
                $layout = $this->layouts->current();
                $this->layouts->next();

                include $this->templateResolver->getLayout($layout);
            } else {
                $this->htmlRenderer->render($this->model);
            }
        } finally {
            $content = ob_get_clean();
        }

        return $content;
    }

    public function render(AbstractViewModel $model)
    {
        $this->model = $model;

        echo $this->content();
    }

    public function match(HttpRequestInterface $httpRequest, MvcRequest $mvcRequest)
    {
        if (
            $httpRequest->isJsonRequest() ||
            $httpRequest->isXmlHttpRequest() ||
            $mvcRequest->isSubRequest()
        ) {
            return false;
        }

        return true;
    }
}
