<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Package;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadPackages()
    {
        $loader = new Loader(['Venyii\HipChatCommander\Test\Package\Dummy1']);

        $this->assertCount(1, $loader->getPackages());
        $this->assertInstanceOf('\Venyii\HipChatCommander\Package\AbstractPackage', $loader->getPackage('dummy1'));
    }

    public function testGetPackage()
    {
        $loader = new Loader(['\Venyii\HipChatCommander\Test\Package\Dummy1']);

        $this->assertInstanceOf('Venyii\HipChatCommander\Package\AbstractPackage', $loader->getPackage('dummy1'));
    }

    public function testGetPackageReturnsNullIfNoResult()
    {
        $loader = new Loader(['\Venyii\HipChatCommander\Test\Package\Dummy1']);

        $this->assertNull($loader->getPackage('imaginary'));
    }

    public function testNotExistingPackageThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException', 'The package "SearchMe\IfYou\Can\Package" could not be found');

        new Loader(['SearchMe\IfYou\Can']);
    }

    public function testPackageMissingExtendsThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException', 'The package "\Venyii\HipChatCommander\Test\Package\MissingExtends\Package" must extend the Venyii\HipChatCommander\Package\AbstractPackage class');

        new Loader(['\Venyii\HipChatCommander\Test\Package\MissingExtends']);
    }
}
