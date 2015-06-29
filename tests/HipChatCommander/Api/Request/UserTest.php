<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Api\Request;

use Venyii\HipChatCommander\Api\Request\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    private $testInstance;

    protected function setUp()
    {
        $this->testInstance = new User(235423, 'name', 'mentionName');
    }

    public function testId()
    {
        $this->assertSame(235423, $this->testInstance->getId());
    }

    public function testGetName()
    {
        $this->assertSame('name', $this->testInstance->getName());
    }

    public function testGetMentionName()
    {
        $this->assertSame('mentionName', $this->testInstance->getMentionName());
    }
}
