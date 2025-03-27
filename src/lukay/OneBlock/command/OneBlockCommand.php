<?php

declare(strict_types=1);

namespace lukay\OneBlock\command;

use lukay\OneBlock\command\subcommand\CreateSubcommand;
use lukay\OneBlock\command\subcommand\Subcommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class OneBlockCommand extends Command{

    private array $subcommands = [];

    public function __construct() {
        parent::__construct("oneblock", "", null, ["ob"]);

        $this->setPermission("oneblock.command");

        $this->registerSubcommand(new CreateSubcommand("create"));
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (count($args) === 0) {
            return;
        }

        $subcommand = strtolower(array_shift($args));
        if (!isset($this->subcommands[$subcommand])) {
            return;
        }

        $command = $this->subcommands[$subcommand];
        if (!$command->testPermission($sender)) {
            return;
        }

        if ($command instanceof SubCommand) {
            $command->execute($sender, $args);
        }
    }

    public function registerSubcommand(SubCommand $subCommand): void {
        $this->subcommands[$subCommand->getName()] = $subCommand;
        foreach ($subCommand->getAliases() as $alias) {
            $this->subcommands[$alias] = $subCommand;
        }
    }
}