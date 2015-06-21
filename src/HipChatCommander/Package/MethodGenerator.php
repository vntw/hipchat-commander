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

class MethodGenerator
{
    /**
     * @param Command $command
     *
     * @return string
     */
    public function generate(Command $command)
    {
        $origMethodName = $command->getMethod() ?: $command->getName();

        $methodNameParts = explode('-', $origMethodName);
        $methodNameStart = array_shift($methodNameParts);
        $methodNameEnd = implode('', array_map('ucfirst', $methodNameParts));

        return $methodNameStart.$methodNameEnd.'Cmd';
    }
}
