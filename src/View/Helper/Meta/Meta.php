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
    private $metas = [];

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(array $attributes)
    {
        if (!$this->metas) {
            $this->eventDispatcher->addSubscriber($this);
        }
        $this->metas[] = $attributes;
    }

    public function onMeta()
    {
        foreach ($this->metas as $meta) {
            echo '<meta';
            foreach ($meta as $attributeName => $attributeValue) {
                echo ' '.$attributeName.'="'.$attributeValue.'"';
            }
            echo ">\n";
        }
    }

    public function getSubscribedEvents()
    {
        return [
            'meta' => 'onMeta',
        ];
    }
}
