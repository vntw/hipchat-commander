<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cilex\Provider\Console\Adapter\Silex\ConsoleServiceProvider;
use Symfony\Component\Console\Application as ConsoleApplication;
use Venyii\HipChatCommander\Console\Command;

require __DIR__.'/bootstrap.php';

$app->register(new ConsoleServiceProvider(), ['console.name' => 'HC-Commander']);

/** @var ConsoleApplication $console */
$console = $app['console'];

$console->addCommands([
    new Command\ClearCache(),
    new Command\ListClients(),
    new Command\UninstallClients(),
]);

$console->run();
