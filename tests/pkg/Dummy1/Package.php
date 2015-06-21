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
            ->addCommand('do', [], true)
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
