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

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class JsonRenderer implements ViewRendererMatcherInterface
{
    public function __construct()
    {
    }

    public function match(HttpRequestInterface $httpRequest, MvcRequest $mvcRequest)
    {
        return $httpRequest->isJsonRequest();
    }

    public function render(AbstractViewModel $viewModel)
    {
    }

    public function isRenderable(AbstractViewModel $model)
    {
    }
}
