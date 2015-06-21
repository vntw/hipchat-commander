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

use Assert\Assertion;

class Response
{
    const COLOR_YELLOW = 'yellow';
    const COLOR_RED = 'red';
    const COLOR_GRAY = 'gray';
    const COLOR_GREEN = 'green';
    const COLOR_PURPLE = 'purple';
    const COLOR_RANDOM = 'random';

    const FORMAT_HTML = 'html';
    const FORMAT_TEXT = 'text';

    private $message;
    private $messageFormat = self::FORMAT_TEXT;
    private $color = self::COLOR_YELLOW;
    private $notify = false;

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setColor($color)
    {
        Assertion::inArray($color, [
            self::COLOR_GREEN, self::COLOR_GRAY, self::COLOR_PURPLE,
            self::COLOR_RED, self::COLOR_YELLOW, self::COLOR_RANDOM,
        ]);
        $this->color = $color;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        Assertion::notEmpty($message);
        $this->message = $message;

        return $this;
    }

    /**
     * @param string $messageFormat
     *
     * @return $this
     */
    public function setMessageFormat($messageFormat)
    {
        Assertion::inArray($messageFormat, [self::FORMAT_TEXT, self::FORMAT_HTML]);
        $this->messageFormat = $messageFormat;

        return $this;
    }

    /**
     * @param bool $notify
     *
     * @return $this
     */
    public function setNotify($notify)
    {
        Assertion::boolean($notify);
        $this->notify = $notify;

        return $this;
    }

    /**
     * @return string
     */
    public function toArray()
    {
        return [
            'color' => $this->color,
            'message' => $this->message,
            'message_format' => $this->messageFormat,
            'notify' => $this->notify,
        ];
    }

    /**
     * @param string      $message
     * @param string|null $format
     * @param string|null $color
     * @param string|null $notify
     *
     * @return Response
     */
    public static function create($message, $format = null, $color = null, $notify = null)
    {
        $response = new self();
        $response->setMessage($message);

        if ($format !== null) {
            $response->setMessageFormat($format);
        }
        if ($color !== null) {
            $response->setColor($color);
        }
        if ($notify !== null) {
            $response->setNotify($notify);
        }

        return $response;
    }

    /**
     * @param string      $message
     * @param string|null $format
     *
     * @return Response
     */
    public static function createSuccess($message, $format = null)
    {
        return self::create('(successful) '.$message, $format, self::COLOR_GREEN);
    }

    /**
     * @param \Exception|string $message
     * @param string|null       $format
     *
     * @return Response
     */
    public static function createError($message, $format = null)
    {
        $message = $message instanceof \Exception ? $message->getMessage() : $message;

        return self::create('(failed) '.$message, $format, self::COLOR_RED);
    }
}
