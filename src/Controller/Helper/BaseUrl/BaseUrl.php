<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\BaseUrl;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Http\Request\HttpRequestInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class BaseUrl implements ActionHelperInterface
{
    private $request;

    public function __construct(HttpRequestInterface $request)
    {
        $this->request = $request;
    }

    public function baseUrl()
    {
        return $this->request->getBaseUrl();
    }

    public function __toString()
    {
        return $this->baseUrl();
    }
}
