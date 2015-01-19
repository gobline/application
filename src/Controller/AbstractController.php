<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller;

use Mendo\Mvc\ViewModel\AbstractViewModel;
use Pimple\Container;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
abstract class AbstractController
{
    protected $helperContainer;
    protected $viewModel;

    public function init()
    {
    }

    public function getViewModel()
    {
        return $this->viewModel;
    }

    public function setViewModel(AbstractViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
    }

    public function setHelperContainer(Container $helperContainer)
    {
        $this->helperContainer = $helperContainer;
    }

    public function __get($helperName)
    {
        return $this->helperContainer[$helperName];
    }

    public function __call($helperName, array $arguments)
    {
        $helper = $this->helperContainer[$helperName];

        return call_user_func_array([$helper, $helperName], $arguments);
    }
}
