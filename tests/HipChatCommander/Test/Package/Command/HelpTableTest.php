<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Package\Command;

use Venyii\HipChatCommander\Package\Command;
use Venyii\HipChatCommander\Package\Command\HelpTable;
use Venyii\HipChatCommander\Test\Package\Dummy1;
use Venyii\HipChatCommander\Test\Package\NoCommands;

class HelpTableTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $pkg = new Dummy1\Package();
        $pkg->configure();

        $helpTable = new HelpTable($pkg);
        $output = $helpTable->build();

        $expected = <<<TABLE
<strong>dummy1</strong> - Dummy1 package description
<br><br>
<table>
    <tr>
        <td>Command</td>
        <td>Aliases</td>
        <td>Description</td>
    </tr>
    <tr><td>do [default]&nbsp;</td><td>-&nbsp;</td><td>This is the do command&nbsp;</td></tr><tr><td>make&nbsp;</td><td>build, create&nbsp;</td><td>This is some command description&nbsp;</td></tr><tr><td>produce&nbsp;</td><td>-&nbsp;</td><td>-&nbsp;</td></tr>
</table>
TABLE;

        $this->assertSame($expected, $output);
    }

    public function testBuildThrowsExceptionIfPackageHasNoCommands()
    {
        $this->setExpectedException('\RuntimeException', 'Creating a help table without commands is not possible.');

        $pkg = new NoCommands\Package();
        $pkg->configure();

        $helpTable = new HelpTable($pkg);
        $helpTable->build();
    }
}
