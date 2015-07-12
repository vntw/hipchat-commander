<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Config;

use Venyii\HipChatCommander\Config\Room;

class RoomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Room
     */
    private $testInstance;

    protected function setUp()
    {
        $this->testInstance = new Room(235423, ['pkg1' => [], 'pkg2' => []]);
    }

    public function testId()
    {
        $this->assertSame(235423, $this->testInstance->getId());
    }

    public function testGetPackageByName()
    {
        $this->assertNotNull('array', $this->testInstance->getPackageByName('pkg1'));
        $this->assertInternalType('array', $this->testInstance->getPackageByName('pkg1'));
    }

    public function testGetPackageByNameReturnsNull()
    {
        $this->assertNull($this->testInstance->getPackageByName('none'));
    }
}
