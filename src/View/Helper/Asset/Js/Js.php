<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Asset\Js;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\View\Helper\Asset\AbstractAssetHelper;
use Mendo\Mvc\View\Helper\Asset\AssetVersions;
use Mendo\Mvc\View\Helper\Asset\MinifierInterface;
use Mendo\Mvc\View\Helper\Asset\ModuleAssetCopier;
use Mendo\Mediator\EventDispatcherInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Js extends AbstractAssetHelper implements ViewHelperInterface
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

    public function js($path, $location = 'body', $isModuleAsset = false, $ieConditionalComment = null)
    {
        $this->asset = new Script($path, $location, $isModuleAsset, $ieConditionalComment);
        $this->eventDispatcher->addSubscriber($this);

        return $this;
    }

    protected function printReference($path)
    {
        echo '<script src="'.$path."\"></script>\n";
    }

    protected function printInternalContent($data)
    {
        echo '<script>'.$data."</script>\n";
    }

    public function onHeadScripts()
    {
        if ('head' === $this->asset->getLocation()) {
            $this->printAsset();
        }
    }

    public function onBodyScripts()
    {
        if ('body' === $this->asset->getLocation()) {
            $this->printAsset();
        }
    }

    public function getSubscribedEvents()
    {
        return [
            'headScripts' => 'onHeadScripts',
            'bodyScripts' => 'onBodyScripts',
        ];
    }
}
