<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Package\Command;

use Venyii\HipChatCommander\Package\AbstractPackage;
use Venyii\HipChatCommander\Package\Command;

class HelpTable
{
    /**
     * @var AbstractPackage
     */
    private $package;

    /**
     * @var array
     */
    private $ignoredCommands = ['help'];

    /**
     * @param AbstractPackage $package
     */
    public function __construct(AbstractPackage $package)
    {
        $this->package = $package;
    }

    /**
     * @return string
     */
    public function build()
    {
        if (empty($this->package->getCommands())) {
            throw new \RuntimeException('Creating a help table without commands is not possible.');
        }

        $table = <<<HTML
<strong>%s</strong>%s
<br><br>
<table>
    <tr>
        <td>Command</td>
        <td>Aliases</td>
        <td>Description</td>
    </tr>
    %s
</table>
HTML;

        $name = $this->package->getName();
        $description = null;

        if ($this->package->getDescription()) {
            $description .= ' - '.htmlspecialchars($this->package->getDescription());
        }

        $commands = $this->buildCommandRows();

        return sprintf($table, $name, $description, $commands);
    }

    /**
     * @return string
     */
    private function buildCommandRows()
    {
        $commands = '';
        foreach ($this->package->getCommands() as $command) {
            if (in_array($command->getName(), $this->ignoredCommands, true)) {
                continue;
            }

            $aliases = '-';

            if (!empty($command->getAliases())) {
                $aliases = implode(', ', $command->getAliases());
            }

            $commands .= sprintf(
                '<tr><td>%s%s&nbsp;</td><td>%s&nbsp;</td><td>%s&nbsp;</td></tr>',
                $command->getName(),
                $command->isDefault() ? ' [default]' : null,
                $aliases,
                $command->getDescription() ? htmlspecialchars($command->getDescription()) : '-'
            );
        }

        return $commands;
    }
}
