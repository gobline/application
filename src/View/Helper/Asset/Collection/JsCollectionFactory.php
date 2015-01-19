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

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\Asset\AssetVersions;
use Mendo\Mvc\View\Helper\Asset\MinifierInterface;
use Mendo\Mvc\View\Helper\Asset\ModuleAssetCopier;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class JsCollectionFactory implements ViewHelperInterface
{
    private $collections = [];
    private $eventDispatcher;
    private $assetVersions;
    private $minifier;
    private $moduleAssetCopier;
    private $baseUrl;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        AssetVersions $assetVersions,
        MinifierInterface $minifier,
        ModuleAssetCopier $moduleAssetCopier,
        $baseUrl
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->assetVersions = $assetVersions;
        $this->minifier = $minifier;
        $this->moduleAssetCopier = $moduleAssetCopier;
        $this->baseUrl = $baseUrl;
    }

    public function jsCollection($path, $location = 'body', $ieConditionalComment = null)
    {
        if (!isset($this->collections[$path])) {
            $this->collections[$path] = new JsCollection(
                new Collection($path, $location, $ieConditionalComment),
                $this->eventDispatcher,
                $this->assetVersions,
                $this->minifier,
                $this->moduleAssetCopier,
                $this->baseUrl);
        }

        return $this->collections[$path];
    }
}
