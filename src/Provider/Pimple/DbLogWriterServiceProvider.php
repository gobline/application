<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Provider\Pimple;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Mendo\Logger\Writer\TableMetadata;
use Mendo\Logger\Writer\TableDataProviderCallback;
use Mendo\Logger\Writer\DbLogWriter;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DbLogWriterServiceProvider implements ServiceProviderInterface
{
    private $reference;

    public function __construct($reference = 'logger.writer.db')
    {
        $this->reference = $reference;
    }

    public function register(Container $container)
    {
        $reference = $this->reference;

        $container[$reference.'.table'] = 'logs';

        $container[$reference.'.column.date'] = 'date';
        $container[$reference.'.column.level'] = 'level';
        $container[$reference.'.column.message'] = 'message';
        $container[$reference.'.column.exception_code'] = 'exception_code';
        $container[$reference.'.column.exception_stack_trace'] = 'exception_stack_trace';

        $container[$reference.'.column.http_url'] = 'http_url';
        $container[$reference.'.column.http_method'] = 'http_method';
        $container[$reference.'.column.http_language'] = 'http_language';
        $container[$reference.'.column.http_ajax'] = 'http_ajax';
        $container[$reference.'.column.http_json'] = 'http_json';

        $container[$reference.'.column.mvc_route'] = 'mvc_route';
        $container[$reference.'.column.mvc_module'] = 'mvc_module';
        $container[$reference.'.column.mvc_controller'] = 'mvc_controller';
        $container[$reference.'.column.mvc_action'] = 'mvc_action';
        $container[$reference.'.column.mvc_params'] = 'mvc_params';
        $container[$reference.'.column.mvc_forwarded'] = 'mvc_forwarded';
        $container[$reference.'.column.mvc_dispatched'] = 'mvc_dispatched';

        $container[$reference.'.column.user_authenticated'] = 'user_authenticated';
        $container[$reference.'.column.user_id'] = 'user_id';
        $container[$reference.'.column.user_login'] = 'user_login';
        $container[$reference.'.column.user_role'] = 'user_role';
        $container[$reference.'.column.user_data'] = 'user_data';

        $container[$reference.'.dataProvider'] = $container->protect(function () use ($container, $reference) {
            $data = [];
            try {
                $httpRequest = $container['request.http'];
                $data = array_merge($data, [
                     $container[$reference.'.column.http_url']      => $httpRequest->getUrl(),
                     $container[$reference.'.column.http_method']   => $httpRequest->getMethod(),
                     $container[$reference.'.column.http_language'] => $httpRequest->getLanguage(),
                     $container[$reference.'.column.http_ajax']     => $httpRequest->isAjax(),
                     $container[$reference.'.column.http_json']     => $httpRequest->isJsonRequest(),
                ]);
            } catch (Exception $e) {
            }
            try {
                $mvcRequest = $container['request.mvc'];
                $data = array_merge($data, [
                     $container[$reference.'.column.mvc_route']      => $mvcRequest->getRoute(),
                     $container[$reference.'.column.mvc_module']     => $mvcRequest->getModule(),
                     $container[$reference.'.column.mvc_controller'] => $mvcRequest->getController(),
                     $container[$reference.'.column.mvc_action']     => $mvcRequest->getAction(),
                     $container[$reference.'.column.mvc_params']     => print_r($mvcRequest->getParams(), true),
                     $container[$reference.'.column.mvc_forwarded']  => $mvcRequest->isForwarded(),
                     $container[$reference.'.column.mvc_dispatched'] => $mvcRequest->isDispatched(),
                ]);
            } catch (Exception $e) {
            }
            try {
                $auth = $container['auth'];
                $data = array_merge($data, [
                     $container[$reference.'.column.user_authenticated'] => $auth->isAuthenticated(),
                     $container[$reference.'.column.user_id']            => $auth->getId(),
                     $container[$reference.'.column.user_login']         => $auth->getLogin(),
                     $container[$reference.'.column.user_role']          => $auth->getRole(),
                     $container[$reference.'.column.user_data']          => print_r($auth->getData(), true),
                ]);
            } catch (Exception $e) {
            }

            return $data;
        });

        $container[$reference] = function ($c) use ($reference) {
            if (empty($c[$reference.'.db'])) {
                throw new \Exception('db not specified');
            }
            if (empty($c[$c[$reference.'.db']])) {
                throw new \Exception('db not found');
            }

            $metadata = new TableMetadata($c[$reference.'.table']);
            $metadata
                ->setColumnDate($c[$reference.'.column.date'])
                ->setColumnLevel($c[$reference.'.column.level'])
                ->setColumnMessage($c[$reference.'.column.message'])
                ->setColumnExceptionCode($c[$reference.'.column.exception_code'])
                ->setColumnExceptionStackTrace($c[$reference.'.column.exception_stack_trace']);

            $writer = new DbLogWriter($c[$c[$reference.'.db']], $metadata);
            $writer->addDataProvider(new TableDataProviderCallback($c[$reference.'.dataProvider']));

            return $writer;
        };
    }
}
