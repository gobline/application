<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Request;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Http\Request\HttpRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Request implements ActionHelperInterface
{
    private $mvcRequest;
    private $httpRequest;

    public function __construct(MvcRequest $mvcRequest, HttpRequest $httpRequest)
    {
        $this->mvcRequest = $mvcRequest;
        $this->httpRequest = $httpRequest;
    }

    public function __call($method, array $arguments)
    {
        if (is_callable([$this->httpRequest, $method])) {
            return $this->httpRequest->$method(...$arguments);
        }

        if (is_callable([$this->mvcRequest, $method])) {
            return $this->mvcRequest->$method(...$arguments);
        }

        throw new \BadMethodCallException('Method "'.$method.'" not found');
    }

    public function request()
    {
        return $this;
    }
}
