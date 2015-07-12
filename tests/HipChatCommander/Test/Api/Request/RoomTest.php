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

use Venyii\HipChatCommander\Api\Request\Room;

class RoomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Room
     */
    private $testInstance;

    protected function setUp()
    {
        $this->testInstance = new Room(235423, 'room1');
    }

    public function testId()
    {
        $this->assertSame(235423, $this->testInstance->getId());
    }

    public function testGetName()
    {
        $this->assertSame('room1', $this->testInstance->getName());
    }
}
