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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCache extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->addOption('namespace', 's', InputOption::VALUE_REQUIRED, 'May be a custom name or the clientId')
            ->addOption('package', 'p', InputOption::VALUE_REQUIRED, 'The package name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getContainer();

        if ($namespace = $input->getOption('namespace')) {
            $packageName = $input->getOption('package');

            if (null !== $packageName && null === $app['hc.package_loader']->getPackage($packageName)) {
                $output->writeln(sprintf('<error>Unknown package "%s"</error>', $packageName));

                return;
            }

            if ($packageName) {
                $output->writeln(sprintf('Clearing cache for namespace "<comment>%s</comment>" and package "<comment>%s</comment>"', $namespace, $packageName));
            } else {
                $output->writeln(sprintf('Clearing cache for namespace "<comment>%s</comment>"', $namespace));
            }

            return;
        }

        $output->writeln('Clearing cache completely');
    }
}
