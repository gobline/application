<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Plugin\SetHttpResponseErrorCode;

use Mendo\Mvc\Request\MvcRequest;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class SetHttpResponseErrorCode
{
    private $request;
    private $codes = [
    	'400' => 'Bad Request',
    	'401' => 'Unauthorized',
    	'403' => 'Forbidden',
    	'404' => 'Not Found',
    	'405' => 'Method Not Allowed',
    	'500' => 'Internal Server Error',
    	'501' => 'Not Implemented',
    	'503' => 'Service Unavailable',
    ];

    public function __construct(MvcRequest $request)
    {
        $this->request = $request;
    }

    public function beforeDispatch()
    {
    	if (headers_sent()) {
    		return;
    	}

        $code = ltrim($this->request->getAction(), '_');

        if (!ctype_digit($code)) {
        	return;
        }

        if (!array_key_exists($code, $this->codes)) {
        	return;
        }

        header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$this->codes[$code], true, $code);
    }
}
