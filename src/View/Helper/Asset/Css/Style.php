<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Asset\Css;

use Mendo\Mvc\View\Helper\Asset\AbstractAsset;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Style extends AbstractAsset
{
    public function __construct($path, $isModuleAsset = false, $ieConditionalComment = null)
    {
        parent::__construct($path, 'head', $isModuleAsset, $ieConditionalComment);
    }
}
