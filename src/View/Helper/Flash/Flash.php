<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Flash;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Flash\FlashInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Flash implements ViewHelperInterface
{
    private $flash;

    public function __construct(FlashInterface $flash)
    {
        $this->flash = $flash;
    }

    public function has($name)
    {
        return $this->flash->has($name);
    }

    public function get(...$args)
    {
        if (count($args) === 1) {
            return $this->flash->get($args[0], '');
        }

        return $this->flash->get(...$args);
    }
}
