<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Descriptor;

use Venyii\HipChatCommander\Descriptor\Builder;
use Venyii\HipChatCommander\Test\TestPackage;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $configMock = $this->getMock('\Venyii\HipChatCommander\Config\Config', [], [], '', false);
        $configMock
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('bot_name'))
            ->will($this->returnValue(Builder::DEFAULT_BOT_NAME))
        ;
        $configMock
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('install.allow_room'))
            ->will($this->returnValue(false))
        ;
        $configMock
            ->expects($this->at(2))
            ->method('get')
            ->with($this->identicalTo('install.allow_global'))
            ->will($this->returnValue(true))
        ;

        $builder = new Builder('https://commander.com', $configMock, $this->createPackageLocatorMock(), 'global');
        $desc = $builder->build();

        $this->assertSame($builder::DEFAULT_BOT_NAME, $desc['name']);
        $this->assertArrayHasKey('description', $desc);
        $this->assertArrayHasKey('key', $desc);
        $this->assertSame('https://commander.com/package.json', $desc['links']['self']);
        $this->assertSame('^\/(package1|package2|package3)', $desc['capabilities']['webhook'][0]['pattern']);
        $this->assertFalse($desc['capabilities']['installable']['allowRoom']);
        $this->assertTrue($desc['capabilities']['installable']['allowGlobal']);
    }

    public function testBuildWithCustomOptions()
    {
        $configMock = $this->getMock('\Venyii\HipChatCommander\Config\Config', [], [], '', false);
        $configMock
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('bot_name'))
            ->will($this->returnValue('ACME Bot'))
        ;
        $configMock
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('install.allow_room'))
            ->will($this->returnValue(true))
        ;
        $configMock
            ->expects($this->at(2))
            ->method('get')
            ->with($this->identicalTo('install.allow_global'))
            ->will($this->returnValue(true))
        ;

        $builder = new Builder('https://commander.com', $configMock, $this->createPackageLocatorMock(), 'global');
        $desc = $builder->build();

        $this->assertSame('ACME Bot', $desc['name']);
        $this->assertArrayHasKey('description', $desc);
        $this->assertArrayHasKey('key', $desc);
        $this->assertSame('https://commander.com/package.json', $desc['links']['self']);
        $this->assertSame('^\/(package1|package2|package3)', $desc['capabilities']['webhook'][0]['pattern']);
        $this->assertTrue($desc['capabilities']['installable']['allowRoom']);
        $this->assertTrue($desc['capabilities']['installable']['allowGlobal']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No packages were found
     */
    public function testNoPackagesThrowsException()
    {
        $configMock = $this->getMock('\Venyii\HipChatCommander\Config\Config', [], [], '', false);
        $builder = new Builder('https://commander.com', $configMock, $this->createPackageLocatorMock(true), 'global');
        $builder->build();
    }

    /**
     * @param bool $empty
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createPackageLocatorMock($empty = false)
    {
        if ($empty) {
            $packages = [];
        } else {
            $packages = [
                new TestPackage('package1'),
                new TestPackage('package2'),
                new TestPackage('package3'),
            ];
        }

        $locatorMock = $this->getMock('\Venyii\HipChatCommander\Package\Locator', [], [], '', false);
        $locatorMock
            ->expects($this->once())
            ->method('getPackages')
            ->will($this->returnValue($packages))
        ;

        return $locatorMock;
    }
}
