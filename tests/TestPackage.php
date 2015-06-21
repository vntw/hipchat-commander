<?php

namespace Venyii\HipChatCommander\Test;

use Venyii\HipChatCommander\Package\AbstractPackage;
use Venyii\HipChatCommander\Package\Command;

class TestPackage extends AbstractPackage
{
    private $name;
    private $commands;

    /**
     * @param string    $name
     * @param Command[] $commands
     */
    public function __construct($name, array $commands = [])
    {
        $this->name = $name;
        $this->commands = $commands;

        $this->configure();
    }

    public function configure()
    {
        $this->setName($this->name);

        foreach ($this->commands as $command) {
            $this->addCommand($command->getName(), $command->getAliases(), $command->isDefault());
        }
    }
}
