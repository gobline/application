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
class AssetVersions
{
    private $assets;

    public function __construct(array $assets = [])
    {
        $this->assets = $assets;
    }

    public function getVersion($asset)
    {
        if (isset($this->assets[$asset])) {
            return $this->assets[$asset];
        }

        return;
    }
}
