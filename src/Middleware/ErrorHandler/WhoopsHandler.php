<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Application\Middleware\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class WhoopsHandler
{
    private $logger;
    private $logLevel;

    public function setLogger(LoggerInterface $logger, $logLevel = 'error')
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;

        return $this;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Throwable $e)
    {
        if ($this->logger) {
            $this->logger->log($this->logLevel, $e->getMessage(), ['exception' => $e]);
        }

        $whoops = new Run();
        $whoops->allowQuit(false);
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();

        $method = Run::EXCEPTION_HANDLER;

        ob_start();
        $whoops->$method($e);
        $content = ob_get_clean();

        return new HtmlResponse($content, 500);
    }
}
