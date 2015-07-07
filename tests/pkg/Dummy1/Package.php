<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Package\Dummy1;

use Venyii\HipChatCommander\Package\AbstractPackage;
use Venyii\HipChatCommander\Api\Response;

class Package extends AbstractPackage
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('dummy1')
            ->setAliases(['dm1', 'dunny1'])
            ->setDescription('Dummy1 package description')
            ->addCommand('do', 'This is the do command', [], true)
            ->addCommand('make', 'This is some command description', ['build', 'create'])
            ->addCommand('produce')
        ;
    }

    /**
     * @return Response
     */
    public function doCmd()
    {
        return Response::create('Dummy!');
    }
}
