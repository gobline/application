<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Request;

use Mendo\Mvc\Router\UrlMaker;
use Mendo\Flash\FlashInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Redirector
{
    private $urlMaker;
    private $flash;

    public function __construct(UrlMaker $urlMaker, FlashInterface $flash)
    {
        $this->urlMaker = $urlMaker;
        $this->flash = $flash;
    }

    public function redirect(MvcRequest $request, array $flashMessages = [], $language = null)
    {
        foreach ($flashMessages as $key => $message) {
            $this->flash->next($key, $message);
        }

        header('Location: '.$this->urlMaker->makeUrl($request, $language));
        exit;
    }
}
