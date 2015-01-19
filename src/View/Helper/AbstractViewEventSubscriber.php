<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
abstract class AbstractViewEventSubscriber implements ViewEventSubscriberInterface
{
    public function onHtmlAttributes()
    {
    }

    public function onHeadOpened()
    {
    }

    public function onHeadStylesheets()
    {
    }

    public function onHeadScripts()
    {
    }

    public function onBodyAttributes()
    {
    }

    public function onBodyOpened()
    {
    }

    public function onBodyScripts()
    {
    }
}
