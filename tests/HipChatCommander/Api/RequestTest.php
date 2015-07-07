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
    /**
     * @param string $cmd
     * @param array  $expectedArgs
     *
     * @dataProvider dataProviderArgs
     */
    public function testArgs($cmd, array $expectedArgs)
    {
        $httpRequest = new HttpFoundation\Request([], [], [], [], [], [], json_encode($this->buildDummyData($cmd)));
        $request = new Api\Request($httpRequest, 'addon');
        $this->assertSame($expectedArgs, $request->getArgs());
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
