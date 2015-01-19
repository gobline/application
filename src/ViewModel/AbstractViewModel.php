<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\ViewModel;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
abstract class AbstractViewModel
{
    private $template;

    public function __get($name)
    {
        return $this->$name();
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = (string) $template;
    }
}
