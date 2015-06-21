<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test;

use Venyii\HipChatCommander\Config\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $testInstance;

    protected function setUp()
    {
        parent::setUp();

        $yml = <<<YML
install:
  allow_room: false
  allow_global: true

packages:
  - Venyii\HipChatCommander\Test\Package\Dummy1
  - Venyii\HipChatCommander\Package\HelloWorld

rooms:
  - id: 7331
    packages:
      - name: dummy1
        cache_ns: dev
        restrict:
          user: [23443]
          cmd:
            - name: do
              user: [1337, 8432]
      - name: helloWorld
YML;

        $this->testInstance = Config::loadYaml($yml);
    }

    public function testGetConfig()
    {
        $this->assertFalse($this->testInstance->get('install.allow_room'));
        $this->assertTrue($this->testInstance->get('install.allow_global'));
    }

    public function testConfigReturnsNullForUnknownKey()
    {
        $this->assertNull($this->testInstance->get('does.not.exist'));
    }

    public function testParsePackages()
    {
        $this->assertCount(1, $this->testInstance->getRooms());
    }

    public function testParseRestrictions()
    {
        $room = $this->testInstance->getRoomById(7331);
        $pkg1 = $room->getPackageByName('dummy1');

        $this->assertTrue($pkg1->isUserPermitted(23443));
        $this->assertFalse($pkg1->isUserPermitted(11111));
        $this->assertTrue($pkg1->isUserPermitted(8432, 'do'));
        $this->assertTrue($pkg1->isUserPermitted(1337, 'do'));
        $this->assertFalse($pkg1->isUserPermitted(13351, 'do'));
        $this->assertFalse($pkg1->isUserPermitted(13351, 'do'));

        $pkg2 = $room->getPackageByName('helloWorld');
        $this->assertTrue($pkg2->isUserPermitted(123));
        $this->assertTrue($pkg2->isUserPermitted(123));

        $yml = <<<YML
install:
  allow_room: false
  allow_global: true

packages:
  - Venyii\HipChatCommander\Test\Package\Dummy1
  - Venyii\HipChatCommander\Package\HelloWorld

rooms:
  - id: 7331
    packages:
      - name: dummy1
        cache_ns: dev
        restrict:
          user: [95432]
          cmd:
            - name: do
              user: [435349, 98353]
      - name: helloWorld
YML;

        $this->testInstance = Config::loadYaml($yml);

        $room = $this->testInstance->getRoomById(7331);
        $pkg1 = $room->getPackageByName('dummy1');

        $this->assertFalse($pkg1->isUserPermitted(24123));
        $this->assertTrue($pkg1->isUserPermitted(98353, 'do'));
        $this->assertTrue($pkg1->isUserPermitted(95432));
        $this->assertTrue($pkg1->isUserPermitted(435349, 'do'));
        $this->assertFalse($pkg1->isUserPermitted(928422, 'do'));

        $pkg2 = $room->getPackageByName('helloWorld');
        $this->assertTrue($pkg2->isUserPermitted(123));
        $this->assertTrue($pkg2->isUserPermitted(45454));

        $yml = <<<YML
install:
  allow_room: false
  allow_global: true

packages:
  - Venyii\HipChatCommander\Test\Package\Dummy1
  - Venyii\HipChatCommander\Package\HelloWorld

rooms:
  - id: 7331
    packages:
      - name: dummy1
        restrict:
          cmd:
            - name: do
              user: [565435]
      - name: helloWorld
YML;

        $this->testInstance = Config::loadYaml($yml);

        $room = $this->testInstance->getRoomById(7331);
        $pkg1 = $room->getPackageByName('dummy1');

        $this->assertTrue($pkg1->isUserPermitted(95432));
        $this->assertTrue($pkg1->isUserPermitted(565435, 'do'));
        $this->assertFalse($pkg1->isUserPermitted(95432, 'do'));

        $pkg2 = $room->getPackageByName('helloWorld');
        $this->assertTrue($pkg2->isUserPermitted(123));
        $this->assertTrue($pkg2->isUserPermitted(45454));
    }

    public function testUsePackageDefaults()
    {
        $yml = <<<YML
install:
  allow_room: false
  allow_global: true

packages:
  - Venyii\HipChatCommander\Test\Package\Dummy1
  - Venyii\HipChatCommander\Package\HelloWorld

rooms:
  - id: 7331
    packages:
      - name: helloWorld
        cache_ns: old_value
        default: lounge-default

defaults:
  helloWorld:
    lounge-default:
      cache_ns: new_value
      restrict:
        cmd:
          - name: crawl
            user: [23527, 15564939]
YML;

        $this->testInstance = Config::loadYaml($yml);

        $room = $this->testInstance->getRoomById(7331);

        $this->assertSame('new_value', $room->getPackageByName('helloWorld')->getCacheNs());
    }
}
