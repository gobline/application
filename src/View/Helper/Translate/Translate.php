<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Translate;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Translator\Translator;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Translate implements ViewHelperInterface
{
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function translate($str, array $params = null, $language = null)
    {
        return $this->translator->translate($str, $params, $language);
    }
}
