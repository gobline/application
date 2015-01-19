<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\ActiveMenuClass;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ActiveMenuClass implements ViewHelperInterface
{
    private $mvcRequest;
    private $activeMenuClass;

    public function __construct(MvcRequest $mvcRequest, $activeMenuClass = 'active')
    {
        $this->mvcRequest = $mvcRequest;
        $this->activeMenuClass = ' '.$activeMenuClass.' ';
    }

    public function activeMenuClass($module, $controller = null, $action = null)
    {
        if ($module !== $this->mvcRequest->getModule()) {
            return '';
        }

        if (!$controller) {
            return $this->activeMenuClass;
        }

        if ($controller !== $this->mvcRequest->getController()) {
            return '';
        }

        if (!$action) {
            return $this->activeMenuClass;
        }

        if ($action !== $this->mvcRequest->getAction()) {
            return '';
        }

        return $this->activeMenuClass;
    }
}
