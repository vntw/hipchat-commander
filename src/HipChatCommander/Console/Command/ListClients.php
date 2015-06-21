<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListClients extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('clients:list');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getContainer();
        $installs = $app['hc.api_registry']->getInstalls();

        if (empty($installs)) {
            $output->writeln('<info>No clients currently installed.</info>');

            return;
        }

        $table = new Table($output);
        $table->setHeaders(['Client-Id', 'Install-Date', 'Group-Id', 'Room-Id', 'Oauth-Id', 'Oauth-Secret', 'Auth-Token', 'Expire-Date']);

        foreach ($installs as $clientId => $install) {
            $table->addRow([
                $clientId,
                $install['date']->format(DATE_W3C),
                $install['groupId'],
                $install['roomId'] ?: '-',
                $install['credentials']['oauthId'],
                $install['credentials']['oauthSecret'],
                $install['credentials']['authToken'],
                $install['credentials']['expires']->format(DATE_W3C),
            ]);
        }

        $table->render();
    }
}
