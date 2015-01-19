<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Asset;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
interface MinifierInterface
{
    public function minify($minify);
}
