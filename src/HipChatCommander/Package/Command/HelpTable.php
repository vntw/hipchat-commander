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

use Symfony\Component\Console\Helper\Table;
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
            return '';
        }

        $table = <<<HTML
%s
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
        if ($this->package->getDescription()) {
            $name .= ' - '.htmlspecialchars($this->package->getDescription());
        }

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
                '<tr><td>%s%s</td><td>%s</td><td>%s</td></tr>',
                $command->getName(),
                $command->isDefault() ? ' [default]' : null,
                $aliases,
                $command->getDescription() ? htmlspecialchars($command->getDescription()) : '-'
            );
        }

        return sprintf($table, $name, $commands);
    }
}
