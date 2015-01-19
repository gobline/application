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

use Mendo\Mvc\ViewModel\AbstractViewModel;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
interface ViewRendererInterface
{
    public function render(AbstractViewModel $viewModel);
}
