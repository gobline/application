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

use Mendo\Mvc\View\Helper\Asset\AssetVersions;
use Mendo\Mvc\View\Helper\Asset\MinifierInterface;
use Mendo\Mvc\View\Helper\Asset\ModuleAssetCopier;
use Mendo\Mvc\View\Helper\AbstractViewEventSubscriber;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
abstract class AbstractCollection extends AbstractViewEventSubscriber
{
    protected $collection;
    private $minify = false;
    private $assetVersions;
    private $minifier;
    private $moduleAssetCopier;
    private $baseUrl;

    public function __construct(
        Collection $collection,
        AssetVersions $assetVersions,
        MinifierInterface $minifier,
        ModuleAssetCopier $moduleAssetCopier,
        $baseUrl
    ) {
        $this->collection = $collection;
        $this->assetVersions = $assetVersions;
        $this->minifier = $minifier;
        $this->moduleAssetCopier = $moduleAssetCopier;
        $this->baseUrl = $baseUrl;
    }

    public function minify($path)
    {
        $this->minify = $path;

        return $this;
    }

    private function computeVersion(Collection $collection)
    {
        $sumVersions = 0;
        foreach ($collection as $asset) {
            $version = $this->assetVersions->getVersion($asset->getPath());
            if ($version) {
                $sumVersions += $version;
            }
        }

        return $sumVersions;
    }

    private function merge(Collection $collection)
    {
        $merged = '';
        foreach ($collection as $asset) {
            if ($asset->isExternal()) {
                $assetPath = (strpos($asset->getPath(), '//') === 0) ? 'http:'.$asset->getPath() : $asset->getPath();
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $assetPath);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                $content = curl_exec($curl);
                curl_close($curl);
                if ($content !== false) {
                    $merged .= $content;
                }
            } else {
                if ($asset->isModuleAsset()) {
                    $this->moduleAssetCopier->copy($asset);
                }
                $merged .= file_get_contents(getcwd().'/public/'.$asset->getPath());
            }
        }

        return $merged;
    }

    private function save($path, $content)
    {
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $content);
    }

    abstract protected function printHtml($path);

    protected function printCollection()
    {
        if ($this->collection->getIeConditionalComment()) {
            echo '<!--[if '.$this->collection->getIeConditionalComment()."]>\n";
        }
        $version = $this->computeVersion($this->collection);
        $path = $this->minify ?: $this->collection->getPath();
        if ($version) {
            $pathinfo = pathinfo($path);
            $path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'.'.$version.'.'.$pathinfo['extension'];
        }
        $absolutePath = getcwd().'/public/'.$path;
        if (!is_file($absolutePath)) {
            $content = $this->merge($this->collection);
            if ($this->minify) {
                $content = $this->minifier->minify($content);
                $this->save($absolutePath, $content);
                $this->printHtml($this->baseUrl.'/'.$path);
            } else {
                $this->save($absolutePath, $content);
                $this->printHtml($this->baseUrl.'/'.$path);
            }
        } else {
            $this->printHtml($this->baseUrl.'/'.$path);
        }
        if ($this->collection->getIeConditionalComment()) {
            echo "<![endif]-->\n";
        }
    }
}
