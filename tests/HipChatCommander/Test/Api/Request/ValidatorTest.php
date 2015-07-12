<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Api\Request;

use Symfony\Component\HttpFoundation;
use Venyii\HipChatCommander\Api;
use Venyii\HipChatCommander\WebTestCase;

class ValidatorTest extends WebTestCase
{
    public function testValidate()
    {
        $registryMock = $this->getMockBuilder('Venyii\HipChatCommander\Api\Client\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $registryMock->expects($this->once())
            ->method('isInstalled')
            ->with('__oauthId__')
            ->willReturn(true);

        $httpRequest = new HttpFoundation\Request([], [], [], [], [], [], json_encode($this->buildDummyData('/cmd')));
        $request = new Api\Request($httpRequest, 'addon');

        $validator = new Api\Request\Validator($registryMock);
        $validator->validate($request);
    }

    public function testValidateThrowsExceptionForUnknownClientId()
    {
        $this->setExpectedException('\Exception', 'Unknown client: __oauthId__');

        $registryMock = $this->getMockBuilder('Venyii\HipChatCommander\Api\Client\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $registryMock->expects($this->once())
            ->method('isInstalled')
            ->with('__oauthId__')
            ->willReturn(false);

        $httpRequest = new HttpFoundation\Request([], [], [], [], [], [], json_encode($this->buildDummyData('/cmd')));
        $request = new Api\Request($httpRequest, 'addon');

        $validator = new Api\Request\Validator($registryMock);
        $validator->validate($request);
    }

    /**
     * @return array
     */
    public static function dataProviderArgs()
    {
        return [
            ['/cmd test  test2   something ', ['test', 'test2', 'something']],
            ['/cmd test-test test_test', ['test-test', 'test_test']],
            ['/cmd', []],
            ['/cmd        ', []],
        ];
    }
}
