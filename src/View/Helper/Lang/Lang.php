<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Lang;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\AbstractViewEventSubscriber;
use Mendo\Mediator\EventDispatcherInterface;
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Lang extends AbstractViewEventSubscriber implements ViewHelperInterface
{
    private $eventDispatcher;
    private $request;

    public function __construct(EventDispatcherInterface $eventDispatcher, HttpRequestInterface $request)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->request = $request;
    }

    public function __invoke($lang = true)
    {
        if ($lang) {
            $this->eventDispatcher->addSubscriber($this);
        } else {
            $this->eventDispatcher->removeSubscriber($this);
        }

        return $this->request->getLanguage();
    }

    public function onHtmlAttributes()
    {
        if (!$this->request->getLanguage()) {
            return;
        }
        echo ' lang="'.$this->request->getLanguage().'"';
    }

    public function getSubscribedEvents()
    {
        return [
            'htmlAttributes' => 'onHtmlAttributes',
        ];
    }

    public function __toString()
    {
        return $this->request->getLanguage();
    }
}
