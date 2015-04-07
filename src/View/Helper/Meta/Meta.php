<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Meta;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\AbstractViewEventSubscriber;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Meta extends AbstractViewEventSubscriber implements ViewHelperInterface
{
    private $eventDispatcher;
    private $attributes;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function meta(array $attributes)
    {
        $this->attributes = $attributes;
        $this->eventDispatcher->addSubscriber($this);
    }

    public function onMeta()
    {
        echo '<meta';
        foreach ($this->attributes as $attributeName => $attributeValue) {
            echo ' '.$attributeName.'="'.$attributeValue.'"';
        }
        echo ">\n";
    }

    public function getSubscribedEvents()
    {
        return [
            'meta' => 'onMeta',
        ];
    }
}
