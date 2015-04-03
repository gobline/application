<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Router;

use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Router\I18n\TranslatorAwareTrait;
use Mendo\Router\AbstractRouter;
use Mendo\Router\RouteData;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class TemplateRouter extends AbstractRouter
{
    use TranslatorAwareTrait;

    private $templates = [];
    private $defaultCatchallModule;

    public function __construct()
    {
        parent::__construct('template');
    }

    public function setTemplates($module, array $templates)
    {
        $this->templates[$module] = $templates;
    }

    public function setDefaultCatchallModule($module)
    {
        $this->defaultCatchallModule = $module;
    }

    public function match(HttpRequestInterface $httpRequest)
    {
        $uri = $httpRequest->getPath();

        if (empty(ltrim($uri, '/'))) {
            return false;
        }

        $template = null;
        $module = null;

        $language = $httpRequest->getLanguage();
        if ($this->translator && $language && $this->translator->hasTranslations($language)) {
            $template = array_search($uri, $this->translator->getTranslations($language));

            if (!$template) {
                if ($this->defaultCatchallModule) {
                    $template = $uri;
                } else {
                    return false;
                }
            }

            foreach ($this->templates as $moduleName => $moduleTemplates) {
                if (array_key_exists($template, $moduleTemplates)) {
                    $module = $moduleName;
                    break;
                }
            }

            if (!$module) {
                $module = $this->defaultCatchallModule;
            }
        } else {
            foreach ($this->templates as $moduleName => $moduleTemplates) {
                $template = array_search($uri, $moduleTemplates);
                if ($template) {
                    $module = $moduleName;
                    break;
                }
            }

            if (!$template) {
                if ($this->defaultCatchallModule) {
                    $template = $uri;
                    $module = $this->defaultCatchallModule;
                } else {
                    return false;
                }
            }
        }

        return new RouteData($this->name, [
            '_module' => $module,
            '_template' => ltrim($template, '/'),
        ]);
    }

    public function makeUrl(RouteData $routeData, $language = null, $absolute = false)
    {
        $template = $routeData->getParam('_template');
        $template = $this->leadingSlashIt($template);

        if ($this->translator && $language && $this->translator->hasTranslations($language)) {
            return $this->translator->translate($template, null, $language);
        }

        $module = $routeData->getParam('_module', null);

        if ($module) {
            if (isset($this->templates[$module][$template])) {
                return $this->templates[$module][$template];
            }
        } else {
            foreach ($this->templates as $moduleName => $moduleTemplates) {
                if (isset($moduleTemplates[$template])) {
                    return $moduleTemplates[$template];
                }
            }
        }

        return $template;
    }

    private function leadingSlashIt($s)
    {
        return strlen($s) <= 0 ? '/' : (substr($s, 0, 1) !== '/' ? '/'.$s : $s);
    }
}
