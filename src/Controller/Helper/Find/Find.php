<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Find;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Http\Request\HttpRequest;
use Mendo\Flash\FlashInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Find implements ActionHelperInterface
{
    private $mvcRequest;
    private $httpRequest;
    private $flash;

    public function __construct(
        MvcRequest $mvcRequest,
        HttpRequest $httpRequest,
        FlashInterface $flash
    ) {
        $this->mvcRequest = $mvcRequest;
        $this->httpRequest = $httpRequest;
        $this->flash = $flash;
    }

    public function find(...$args)
    {
        if (count($args) !== 1 && count($args) !== 2) {
            throw new \InvalidArgumentException('find() takes one or two arguments');
        }

        $name = $args[0];

        if ($this->mvcRequest->hasParam($name)) {
            return $this->mvcRequest->getParam($name);
        }

        if ($this->httpRequest->hasPost($name)) {
            return $this->httpRequest->getPost($name);
        }

        if ($this->httpRequest->hasQuery($name)) {
            return $this->httpRequest->getQuery($name);
        }

        if ($this->flash->has($name)) {
            return $this->flash->get($name);
        }

        if (count($args) === 1) {
            throw new \InvalidArgumentException('Parameter "'.$name.'" not found');
        }

        return $args[1];
    }
}
