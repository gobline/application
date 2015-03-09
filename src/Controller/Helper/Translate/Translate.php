<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Translate;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Translator\Translator;
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Translate implements ActionHelperInterface
{
    private $translator;
    private $request;

    public function __construct(Translator $translator, HttpRequestInterface $request)
    {
        $this->translator = $translator;
        $this->request = $request;
    }

    public function translate($str, array $params = null, $language = null)
    {
        if (!$language) {
            $language = $this->request->getLanguage() ?: null;
        }

        return $this->translator->translate($str, $params, $language);
    }
}
