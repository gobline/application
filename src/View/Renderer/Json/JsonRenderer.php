<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Renderer\Json;

use Mendo\Mvc\ViewModel\AbstractViewModel;
use Mendo\Mvc\View\Renderer\ViewRendererMatcherInterface;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Mvc\View\Renderer\TemplateFileResolver;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class JsonRenderer implements ViewRendererMatcherInterface
{
    private $templateResolver;

    public function __construct(TemplateFileResolver $templateResolver)
    {
        $this->templateResolver = $templateResolver;
    }

    public function match(HttpRequestInterface $httpRequest, MvcRequest $mvcRequest)
    {
        return $httpRequest->isJsonRequest();
    }

    public function render(AbstractViewModel $model)
    {
        $content = include $this->templateResolver->getTemplate($model->getTemplate(), 'json');

        echo json_encode($content);
    }

    public function isRenderable(AbstractViewModel $model)
    {
        return true;
    }
}
