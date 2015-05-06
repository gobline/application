<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Title;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\AbstractViewEventSubscriber;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Title extends AbstractViewEventSubscriber implements ViewHelperInterface
{
    private $eventDispatcher;
    private $title;
    private $suffix = '';

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function title($title)
    {
        if (!$this->title) {
            $this->title = (string) $title;
            $this->eventDispatcher->addSubscriber($this);
        }
    }

    public function suffix($suffix)
    {
        if (!$suffix) {
            $this->suffix = '';
            return;
        }

        $this->suffix = $suffix . $this->suffix;
    }

    public function onHeadTitle()
    {
        echo $this->title . $this->suffix;
    }

    public function getSubscribedEvents()
    {
        return [
            'headTitle' => 'onHeadTitle',
        ];
    }
}
