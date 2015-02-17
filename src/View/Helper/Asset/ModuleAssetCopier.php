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

use Mendo\Mvc\Module\ModuleCollection;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ModuleAssetCopier
{
    private $modules;
    private $request;

    public function __construct(ModuleCollection $modules, MvcRequest $request)
    {
        $this->modules = $modules;
        $this->request = $request;
    }

    public function copy(AbstractAsset $asset)
    {
        $moduleName = $this->request->getModule();
        $publicPath = getcwd().'/public/module-assets/'.$moduleName.'/'.$asset->getPath();
        if (!is_file($publicPath)) {
            $modulePath = $this->modules->get($moduleName)->getPath().'assets/'.$asset->getPath();
            if (!is_file($modulePath)) {
                throw new \Exception('Asset not found: '.$modulePath);
            }
            mkdir(dirname($publicPath), 0777, true);
            copy($modulePath, $publicPath);
        }
        $asset->setPath('/module-assets/'.$moduleName.'/'.$asset->getPath());
    }
}
