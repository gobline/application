<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Application\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Gobline\View\HtmlRenderer;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class HtmlRendererMiddleware
{
    private $renderer;

    public function __construct(HtmlRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $template = $request->getAttribute('_view');

        if (!isset($template['text/html'])) {
            return $next($request, $response);
        }

        $template = $template['text/html'];

        $model = $request->getAttribute('_model');

        $layouts = $request->getAttribute('_layouts', []);
        $this->renderer->setLayouts($layouts);

        $this->renderer->enableLayouts();
        if ($request->getAttribute('_isSubRequest')) {
            $this->renderer->disableLayouts();
        }

        return new HtmlResponse($this->renderer->render($template, $model));
    }
}
