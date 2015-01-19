<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Renderer;

use Mendo\Mvc\Request\MvcRequest;
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
interface ViewRendererMatcherInterface extends ViewRendererInterface
{
    public function match(HttpRequestInterface $httpRequest, MvcRequest $mvcRequest);
}
