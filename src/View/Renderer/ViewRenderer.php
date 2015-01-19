<?php

namespace Mendo\Mvc\View\Renderer;

use Mendo\Mvc\ViewModel\AbstractViewModel;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ViewRenderer implements ViewRendererInterface
{
    private $httpRequest;
    private $mvcRequest;
    private $renderers = [];

    public function __construct(HttpRequestInterface $httpRequest, MvcRequest $mvcRequest)
    {
        $this->httpRequest = $httpRequest;
        $this->mvcRequest = $mvcRequest;
    }

    public function addRenderer(ViewRendererInterface $renderer)
    {
        $this->renderers[] = $renderer;
    }

    public function render(AbstractViewModel $model)
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->match($this->httpRequest, $this->mvcRequest)) {
                $renderer->render($model);
                return;
            }
        }

        throw new \RuntimeException('No matching renderer for request "' . $httpRequest->getUrl(true) . '"');
    }
}
