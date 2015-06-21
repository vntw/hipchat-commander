<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Api\Exception;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

class HipChatException extends RequestException
{
    /**
     * @param string            $message
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param \Exception        $previous
     */
    public function __construct($message, RequestInterface $request, ResponseInterface $response = null, \Exception $previous = null)
    {
        parent::__construct($message, $request, $response, $previous);
    }
}
