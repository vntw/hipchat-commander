<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Api;

use Venyii\HipChatCommander\Api;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $response = Api\Response::create('msg', Api\Response::FORMAT_HTML, Api\Response::COLOR_GRAY, true);

        $this->assertSame([
            'color' => Api\Response::COLOR_GRAY,
            'message' => 'msg',
            'message_format' => Api\Response::FORMAT_HTML,
            'notify' => true,
        ], $response->toArray());
    }

    public function testCreateSuccess()
    {
        $response = Api\Response::createSuccess('msg', Api\Response::FORMAT_HTML);

        $this->assertSame([
            'color' => Api\Response::COLOR_GREEN,
            'message' => '(successful) msg',
            'message_format' => Api\Response::FORMAT_HTML,
            'notify' => false,
        ], $response->toArray());
    }

    public function testCreateError()
    {
        $response = Api\Response::createError('msg', Api\Response::FORMAT_TEXT);

        $this->assertSame([
            'color' => Api\Response::COLOR_RED,
            'message' => '(failed) msg',
            'message_format' => Api\Response::FORMAT_TEXT,
            'notify' => false,
        ], $response->toArray());
    }
}
