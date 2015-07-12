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

class MethodGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MethodGenerator
     */
    private $testInstance;

    protected function setUp()
    {
        $this->testInstance = new MethodGenerator();
    }

    protected function tearDown()
    {
        $this->testInstance = null;
    }

    /**
     * @dataProvider methodProvider
     */
    public function testBuild(Command $command, $expectedCmdName)
    {
        $this->assertSame($expectedCmdName, $this->testInstance->generate($command));
    }

    public function methodProvider()
    {
        return [
            [new Command('create'), 'createCmd'],
            [new Command('always', null, [], false, 'day'), 'dayCmd'],
            [new Command('always', null, ['better', 'something'], true, 'day'), 'dayCmd'],
        ];
    }
}
