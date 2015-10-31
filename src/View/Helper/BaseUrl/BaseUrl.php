<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\BaseUrl;

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class BaseUrl implements ViewHelperInterface
{
    private $request;

    public function __construct(HttpRequestInterface $request)
    {
        $this->request = $request;
    }

    public function __toString()
    {
        return $this->request->getBaseUrl();
    }
}
