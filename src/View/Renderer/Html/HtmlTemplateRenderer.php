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
use Mendo\Mvc\View\Renderer\ViewRendererMatcherInterface;
use Mendo\Mvc\View\Renderer\TemplateFileResolver;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class HtmlTemplateRenderer implements ViewRendererMatcherInterface
{
    use ViewHelperTrait;

    private $templateResolver;

    public function __construct(TemplateFileResolver $templateResolver)
    {
        $this->templateResolver = $templateResolver;
    }

    public function partial($template, array $data = [])
    {
        extract($data);

        ob_start();
        try {
            include $this->templateResolver->getPartial($template);
        } finally {
            $content = ob_get_clean();
        }

        return $content;
    }

    public function render(AbstractViewModel $model)
    {
        ob_start();
        try {
            extract($this->getViewHelpers());

            include $this->templateResolver->getTemplate($model->getTemplate());
        } finally {
            $content = ob_get_clean();
        }

        echo $content;
    }

    public function match(HttpRequestInterface $httpRequest, MvcRequest $mvcRequest)
    {
        if ($httpRequest->isJsonRequest()) {
            return false;
        }

        return true;
    }

    public function isRenderable(AbstractViewModel $model)
    {
        return (bool) $this->templateResolver->getTemplate($model->getTemplate());
    }
}
