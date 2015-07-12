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

use Venyii\HipChatCommander\Config\Package;

class PackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Package
     */
    private $testInstance;

    protected function setUp()
    {
        $this->testInstance = new Package('pkg1', 'cache_ns1', []);
    }

    public function testName()
    {
        $this->assertSame('pkg1', $this->testInstance->getName());
    }

    public function testCacheNs()
    {
        $this->assertSame('cache_ns1', $this->testInstance->getCacheNs());
    }
}
