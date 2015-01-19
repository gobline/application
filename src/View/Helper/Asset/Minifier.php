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
class Minifier implements MinifierInterface
{
    public function minify($minify)
    {
        /* remove comments */
        $minify = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify);

        /* remove tabs, spaces, newlines, etc. */
        return str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $minify);
    }
}
