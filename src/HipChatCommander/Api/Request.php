<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Api;

use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Venyii\HipChatCommander\Api\Request\Room;
use Venyii\HipChatCommander\Api\Request\User;

class Request
{
    private $httpRequest;
    private $data;
    private $clientId;
    private $package;
    private $args;
    private $user;
    private $room;

    /**
     * @param HttpRequest $httpRequest
     *
     * @throws \Exception
     */
    public function __construct(HttpRequest $httpRequest)
    {
        $this->httpRequest = $httpRequest;

        $this->parseHttpRequest();

        $this->clientId = $this->data['oauth_client_id'];
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param int $index
     *
     * @return mixed|null
     */
    public function getArg($index)
    {
        return isset($this->args[$index]) ? $this->args[$index] : null;
    }

    /**
     * @param string $arg
     */
    public function setDefaultArg($arg)
    {
        array_unshift($this->args, $arg);
    }

    /**
     * @return mixed
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    private function parseHttpRequest()
    {
        $jsonBodyString = $this->httpRequest->getContent();

        if (!$jsonBodyString) {
            throw new \Exception('Invalid request');
        }

        $json = json_decode($jsonBodyString, true);

        if (json_last_error()) {
            throw new \Exception('JSON error');
        }

        $this->data = $json;

        $this->parseMessage();
        $this->parseUser();
        $this->parseRoom();
    }

    private function parseMessage()
    {
        $message = $this->data['item']['message']['message'];

        preg_match('/^\/([a-zA-Z0-9\-]+)/', $message, $matches);
        $this->package = $matches[1];

        $args = explode(' ', trim(str_replace('/'.$this->package, '', $message)));

        $args = array_values(array_filter($args, function ($arg) {
            return trim($arg) !== '';
        }));

        if (empty($args)) {
            $args = [];
        }

        $this->args = $args;
    }

    private function parseUser()
    {
        $from = $this->data['item']['message']['from'];

        $this->user = new User((int) $from['id'], $from['name'], $from['mention_name']);
    }

    private function parseRoom()
    {
        $room = $this->data['item']['room'];

        $this->room = new Room((int) $room['id'], $room['name']);
    }
}
