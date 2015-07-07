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

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCommand()
    {
        $cmd = new Command('cmd', 'description', ['cmd1', 'cmd2'], true, 'cmdMethod');

        $this->assertSame('cmd', $cmd->getName());
        $this->assertSame('description', $cmd->getDescription());
        $this->assertSame(['cmd1', 'cmd2'], $cmd->getAliases());
        $this->assertTrue($cmd->isDefault());
        $this->assertSame('cmdMethod', $cmd->getMethod());
    }

    public function testConstructThrowsExceptionIfNameIsMissing()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid command options given.');

        new Command(null);
    }

    public function testConstructThrowsExceptionIfAliasesAreInvalid()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid command options given.');

        new Command('cmd', null, 'alias');
    }
}
