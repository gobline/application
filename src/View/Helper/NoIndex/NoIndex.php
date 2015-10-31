<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\NoIndex;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\AbstractViewEventSubscriber;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class NoIndex extends AbstractViewEventSubscriber implements ViewHelperInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke($noIndex = true)
    {
        if ($noIndex) {
            $this->eventDispatcher->addSubscriber($this);
        } else {
            $this->eventDispatcher->removeSubscriber($this);
        }
    }

    public function onMeta()
    {
        echo "<meta name=\"robots\" content=\"noindex, nofollow\">\n";
    }

    public function getSubscribedEvents()
    {
        return [
            'meta' => 'onMeta',
        ];
    }
}
