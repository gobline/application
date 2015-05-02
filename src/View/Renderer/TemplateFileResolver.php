<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Renderer;

use Mendo\Mvc\Request\MvcRequest;
use Mendo\Mvc\Module\ModuleCollection;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class TemplateFileResolver
{
    private $mvcRequest;
    private $modules;

    public function __construct(MvcRequest $mvcRequest, ModuleCollection $modules)
    {
        $this->mvcRequest = $mvcRequest;
        $this->modules = $modules;
    }

    public function getLayout($template)
    {
        return $this->getFile($template, 'layouts/');
    }

    public function getPartial($template)
    {
        return $this->getFile($template, 'partials/');
    }

    public function getTemplate($template, $format = 'html')
    {
        try {
            return $this->getFile($template, 'templates/', $format);
        } catch(Exception\TemplateFileNotFoundException $e) {
            throw new Exception\TemplateFileNotFoundException($e->getMessage(), 404);
        }
    }

    public function getFile($template, $dir, $format = 'html')
    {
        if (!$template) {
            throw new \Exception('empty template name');
        }

        if (is_file($template)) {
            return $template;
        }

        $template .= ($format === 'html' ? '' : '.'.$format).'.phtml';

        foreach ($this->modules->get($this->mvcRequest->getModule())->getTemplatePaths() as $path) {
            if (is_file($path.$dir.$template)) {
                return $path.$dir.$template;
            }
        }

        throw new Exception\TemplateFileNotFoundException($dir.$template);
    }
}
