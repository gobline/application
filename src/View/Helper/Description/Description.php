<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Description;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\AbstractViewEventSubscriber;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Description extends AbstractViewEventSubscriber implements ViewHelperInterface
{
    private $eventDispatcher;
    private $description;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function description($description)
    {
        if (!$this->description) {
            $this->description = (string) $description;
            $this->eventDispatcher->addSubscriber($this);
        }
    }

    public function onMeta()
    {
        echo '<meta name="description" content="'.$this->description."\">\n";
    }

    public function getSubscribedEvents()
    {
        return [
            'meta' => 'onMeta',
        ];
    }
}
