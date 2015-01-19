<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Asset\Collection;

use Mendo\Mvc\View\Helper\Asset\AbstractAsset;
use IteratorAggregate;
use ArrayIterator;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Collection implements IteratorAggregate
{
    private $assets = [];
    private $path;
    private $location;
    private $ieConditionalComment;

    public function __construct($path, $location = 'head', $ieConditionalComment = null)
    {
        $this->path = $path;
        $this->location = $location;
        $this->ieConditionalComment = $ieConditionalComment;
    }

    public function add(AbstractAsset $asset)
    {
        $this->assets[] = $asset;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->assets);
    }

    public function getIeConditionalComment()
    {
        return $this->ieConditionalComment;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getLocation()
    {
        return $this->location;
    }
}
