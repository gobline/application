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
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Translate implements ViewHelperInterface
{
    private $translator;
    private $request;

    public function __construct(Translator $translator, HttpRequestInterface $request)
    {
        $this->translator = $translator;
        $this->request = $request;
    }

    public function __invoke($str, array $params = null, $language = null)
    {
        if (!$language) {
            $language = $this->request->getLanguage() ?: null;
        }

        return $this->translator->translate($str, $params, $language);
    }
}
