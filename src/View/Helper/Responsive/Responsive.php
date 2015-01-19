<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Responsive;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\AbstractViewEventSubscriber;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Responsive extends AbstractViewEventSubscriber implements ViewHelperInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function responsive()
    {
        $this->eventDispatcher->addSubscriber($this);
    }

    public function onMeta()
    {
        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
    }

    public function getSubscribedEvents()
    {
        return [
            'meta' => 'onMeta',
        ];
    }
}
