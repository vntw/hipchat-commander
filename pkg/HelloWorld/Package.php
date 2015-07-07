<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Package\HelloWorld;

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
            ->setName('helloWorld')
            ->setDescription('Greet the world!')
            ->addCommand('help')
            ->addCommand('say', 'Greet the world!', [], true)
        ;
    }

    /**
     * @return Response
     */
    public function sayCmd()
    {
        $emoticons = ['sap', 'rebeccablack', 'shrug', 'yey', 'zoidberg', 'yuno', 'wat'];
        shuffle($emoticons);

        $message = sprintf(
            '(%s) Hello %s and World! (%s)',
            $emoticons[0],
            $this->getRequest()->getUser()->getName(),
            $emoticons[1]
        );

        $this->sendRoomMsg(Response::create('(fry)(giggity)(haha) Addon Response! (fry)(giggity)(haha)'));

        return Response::create($message, null, Response::COLOR_RANDOM);
    }
}
