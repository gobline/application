<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Hreflang;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\AbstractViewEventSubscriber;
use Mendo\Mediator\EventDispatcherInterface;
use Mendo\Mvc\Router\UrlMaker;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Hreflang extends AbstractViewEventSubscriber implements ViewHelperInterface
{
    private $eventDispatcher;
    private $urlMaker;
    private $request;
    private $languages;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UrlMaker $urlMaker,
        MvcRequest $request,
        array $languages
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->urlMaker = $urlMaker;
        $this->request = $request;
        $this->languages = $languages;
    }

    public function __invoke($hreflang = true)
    {
        if ($hreflang) {
            if (count($this->languages) < 2) {
                return;
            }
            $this->eventDispatcher->addSubscriber($this);
        } else {
            $this->eventDispatcher->removeSubscriber($this);
        }
    }

    public function onHeadLinks()
    {
        foreach ($this->languages as $language) {
            echo '<link rel="alternate" hreflang="'.$language.'" href="'.
                $this->urlMaker($this->request, $language)."\">\n";
        }
    }

    public function getSubscribedEvents()
    {
        return [
            'headLinks' => 'onHeadLinks',
        ];
    }
}
