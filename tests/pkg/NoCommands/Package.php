<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Package\NoCommands;

use Venyii\HipChatCommander\Package\AbstractPackage;

class Package extends AbstractPackage
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('noCommands');
    }
}
