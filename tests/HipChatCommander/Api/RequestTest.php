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

use Symfony\Component\HttpFoundation;
use Venyii\HipChatCommander\Api;
use Venyii\HipChatCommander\Test\WebTestCase;

class RequestTest extends WebTestCase
{
    public function testArgs()
    {
        $httpRequest = new HttpFoundation\Request([], [], [], [], [], [], json_encode($this->buildDummyData('/cmd test  test2   something ')));
        $registryMock = $this->getMock('Venyii\HipChatCommander\Api\Client\Registry', [], [], '', false);
        $request = new Api\Request($httpRequest, $registryMock, 'addon');
        $this->assertSame(['test', 'test2', 'something'], $request->getArgs());

        $httpRequest = new HttpFoundation\Request([], [], [], [], [], [], json_encode($this->buildDummyData('/cmd')));
        $registryMock = $this->getMock('Venyii\HipChatCommander\Api\Client\Registry', [], [], '', false);
        $request = new Api\Request($httpRequest, $registryMock, 'addon');
        $this->assertSame([], $request->getArgs());

        $httpRequest = new HttpFoundation\Request([], [], [], [], [], [], json_encode($this->buildDummyData('/cmd        ')));
        $registryMock = $this->getMock('Venyii\HipChatCommander\Api\Client\Registry', [], [], '', false);
        $request = new Api\Request($httpRequest, $registryMock, 'addon');
        $this->assertSame([], $request->getArgs());
    }
}
