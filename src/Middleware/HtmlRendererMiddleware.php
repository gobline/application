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

        $template = isset($template['text/html']) ? $template['text/html'] : null;

        $layouts = $request->getAttribute('_layouts', []);

        if (!$template && !$layouts) {
            return $next($request, $response);
        }

        $this->renderer->enableLayouts();
        $this->renderer->setLayouts($layouts);

        if ($request->getAttribute('_isSubRequest')) {
            $this->renderer->disableLayouts();
        }

        $model = $request->getAttribute('_model');

        return new HtmlResponse($this->renderer->render($template, $model));
    }
}
