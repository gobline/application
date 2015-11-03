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

use Pimple\Container;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
trait ViewHelperTrait
{
    private $helperContainer;

    public function setHelperContainer(Container $helperContainer)
    {
        $this->helperContainer = $helperContainer;
    }

    public function getViewHelpers()
    {
        $array = [];

        foreach ($this->helperContainer->keys() as $helperName) {
            $array[$helperName] = new ViewHelperCallable(
                function () use ($helperName) {
                    return $this->helperContainer[$helperName];
                });
        }

        return $array;
    }
}
