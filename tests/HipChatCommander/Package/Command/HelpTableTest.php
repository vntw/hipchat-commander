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
use Venyii\HipChatCommander\Test\Package\Dummy1\Package;

class HelpTableTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $pkg = new Package();
        $pkg->configure();

        $helpTable = new HelpTable($pkg);
        $output = $helpTable->build();

        $expected = <<<TABLE
dummy1 - Dummy1 package description
<br><br>
<table>
    <tr>
        <td>Command</td>
        <td>Aliases</td>
        <td>Description</td>
    </tr>
    <tr><td>do [default]</td><td>-</td><td>This is the do command</td></tr><tr><td>make</td><td>build, create</td><td>This is some command description</td></tr><tr><td>produce</td><td>-</td><td>-</td></tr>
</table>
TABLE;

        $this->assertSame($expected, $output);
    }
}
