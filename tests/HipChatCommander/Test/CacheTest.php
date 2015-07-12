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
use Venyii\HipChatCommander\WebTestCase;

class CacheTest extends WebTestCase
{
    public function testCacheIsSharedBetweenPackages()
    {
        $yml = <<<YML
packages:
  - Venyii\HipChatCommander\Test\Package\Dummy1
  - Venyii\HipChatCommander\Package\HelloWorld

rooms:
  - id: 7331
    packages:
      - name: dummy1
        cache_ns: shared_1
      - name: helloWorld
  - id: 7332
    packages:
      - name: dummy1
        cache_ns: shared_1
  - id: 7333
    packages:
      - name: dummy1
        cache_ns: ~
YML;

        $this->createTestConfig($yml);

        /* @var Config $config */
        $config = $this->app['hc.config'];
        $room1 = $config->getRoomById(7331);
        $room2 = $config->getRoomById(7332);
        $room3 = $config->getRoomById(7333);

        $pkgName = 'dummy1';
        $pkg1 = $room1->getPackageByName($pkgName);
        $pkg2 = $room2->getPackageByName($pkgName);
        $pkg3 = $room3->getPackageByName($pkgName);

        $clientId = '__clientId__';

        $pkgCache1 = $this->app['hc.pkg_cache']($pkg1->getCacheNs() ?: $clientId, $pkgName);
        $pkgCache2 = $this->app['hc.pkg_cache']($pkg2->getCacheNs() ?: $clientId, $pkgName);
        $pkgCache3 = $this->app['hc.pkg_cache']($pkg3->getCacheNs() ?: $clientId, $pkgName);

        $pkgCache1->save('test_key', 'test_value');

        // $pkgCache1 and $pkgCache2 must be the same namespace
        $this->assertNotFalse($pkgCache2->fetch('test_key'));
        $this->assertSame('test_value', $pkgCache1->fetch('test_key'));
        $this->assertSame('test_value', $pkgCache2->fetch('test_key'));

        $pkgCache2->delete('test_key');

        $this->assertFalse($pkgCache1->fetch('test_key'));
        $this->assertFalse($pkgCache2->fetch('test_key'));

        // $pkgCache3 must not be the same namespace
        $this->assertFalse($pkgCache3->fetch('test_key'));
    }
}
