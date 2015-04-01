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
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class HtmlMasterLayoutRenderer implements ViewRendererMatcherInterface
{
    private $htmlRenderer;
    private $eventDispatcher;

    public function __construct(ViewRendererInterface $htmlRenderer, EventDispatcherInterface $eventDispatcher)
    {
        $this->htmlRenderer = $htmlRenderer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function render(AbstractViewModel $model)
    {
        ob_start();
        try {
            $this->htmlRenderer->render($model);
        } finally {
            $content = ob_get_clean();
        }

        echo "<!DOCTYPE html>\n";
        echo '<html';
        $this->eventDispatcher->dispatch('htmlAttributes');
        echo ">\n";

        echo "<head>\n";
        $this->eventDispatcher->dispatch('headOpened');
        echo "<meta charset=\"utf-8\">\n";
        echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\">\n";

        $this->eventDispatcher->dispatch('meta');

        echo '<title>';
        $this->eventDispatcher->dispatch('headTitle');
        echo "</title>\n";

        // print <link> elements (other than stylesheets)
        $this->eventDispatcher->dispatch('headLinks');

        // print stylesheets
        $this->eventDispatcher->dispatch('headStylesheets');

        // print scripts
        $this->eventDispatcher->dispatch('headScripts');

        // print close head, open body
        echo "</head>\n<body";
        $this->eventDispatcher->dispatch('bodyAttributes');
        echo ">\n";
        $this->eventDispatcher->dispatch('bodyOpened');

        // print body content
        echo $content;

        // print scripts
        $this->eventDispatcher->dispatch('bodyScripts');

        // print close body, close html
        echo "\n</body>\n</html>";
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

    public function isRenderable(AbstractViewModel $model)
    {
        return $this->htmlRenderer->isRenderable($model);
    }
}
