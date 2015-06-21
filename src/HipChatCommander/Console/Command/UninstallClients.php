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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Venyii\HipChatCommander\Api\Client\Registry;

class UninstallClients extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('clients:uninstall')
            ->addArgument('clientIds', InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
            ->addOption('all', 'a', InputOption::VALUE_NONE)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getContainer();

        /** @var Registry $registry */
        $registry = $app['hc.api_registry'];
        $clientIds = $input->getArgument('clientIds');

        if ($input->getOption('all')) {
            $clientIds = array_keys($registry->getInstalls());
        }

        if (empty($clientIds)) {
            $output->writeln('<info>No clients currently installed.</info>');

            return;
        }

        foreach ($clientIds as $clientId) {
            if ($registry->isInstalled($clientId)) {
                $output->writeln(sprintf('<info>Uninstalling client: %s</info>', $clientId));
                $registry->uninstall($clientId);
            } else {
                $output->writeln(sprintf('<fg=black;bg=yellow>WARNING: Client %s is currently not installed!</>', $clientId));
            }
        }
    }
}
