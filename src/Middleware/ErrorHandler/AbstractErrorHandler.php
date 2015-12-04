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
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
abstract class AbstractErrorHandler
{
    private $logger;
    private $logLevel;

    public function setTemplate($template, $accept = 'text/html')
    {
        $this->templates[$accept] = $template;
    }

    public function setLogger(LoggerInterface $logger, $logLevel = 'error')
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;

        return $this;
    }

    abstract protected function getCode();

    protected function getData()
    {
        return [];
    }

    protected function getTemplates()
    {
        return [
            'application/json' => __DIR__.'/templates/'.$this->getCode().'.json.php',
            'text/html' => __DIR__.'/templates/'.$this->getCode().'.html.php',
        ];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Exception $e)
    {
        if ($this->logger) {
            $this->logger->log($this->logLevel, $e->getMessage(), ['exception' => $e]);
        }

        $accept = $request->getHeaderLine('Accept');
        if ($accept && preg_match('#^application/([^+\s]+\+)?json#', $accept)) {
            $content = include $this->templates['application/json'];

            return new JsonResponse($content);
        }

        $data = $this->getData();

        $render = function () use ($data) {
            extract($data);
            include $this->getTemplates()['text/html'];
        };

        ob_start();
        try {
            $render();
        } finally {
            $content = ob_get_clean();
        }

        return new HtmlResponse($content, $this->getCode());
    }
}
