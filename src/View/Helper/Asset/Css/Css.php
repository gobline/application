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

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\Asset\AbstractAssetHelper;
use Mendo\Mvc\View\Helper\Asset\AssetVersions;
use Mendo\Mvc\View\Helper\Asset\MinifierInterface;
use Mendo\Mvc\View\Helper\Asset\ModuleAssetCopier;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Css extends AbstractAssetHelper implements ViewHelperInterface
{
    private $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        AssetVersions $assetVersions,
        MinifierInterface $minifier,
        ModuleAssetCopier $moduleAssetCopier,
        $baseUrl
    ) {
        parent::__construct($assetVersions, $minifier, $moduleAssetCopier, $baseUrl);
        $this->eventDispatcher = $eventDispatcher;
    }

    public function css($path, $isModuleAsset = false, $ieConditionalComment = null)
    {
        $this->asset = new Style($path, $isModuleAsset, $ieConditionalComment);
        $this->eventDispatcher->addSubscriber($this);

        return $this;
    }

    protected function printReference($path)
    {
        echo '<link rel="stylesheet" href="'.$path."\">\n";
    }

    protected function printInternalContent($data)
    {
        echo '<style>'.$data."</style>\n";
    }

    public function onHeadStylesheets()
    {
        $this->printAsset();
    }

    public function getSubscribedEvents()
    {
        return [
            'headStylesheets' => 'onHeadStylesheets',
        ];
    }
}
