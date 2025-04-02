<?php

declare(strict_types=1);

namespace lukay\OneBlock\command\subcommand;

use pocketmine\command\CommandSender;

abstract class Subcommand{
    private string $permission;

    public function __construct
    (
        private readonly string $name,
        private readonly string $usage = "",
        private readonly array  $aliases = [],
    ) {
        $this->permission = $this->name . ".subcommand";
    }

    public function getName() : string {
        return $this->name;
    }

    public function getUsageMessage() : string {
        return $this->usage;
    }

    public function getAliases() : array {
        return $this->aliases;
    }

    public function getPermission() : string {
        return $this->permission;
    }

    public function testPermission(CommandSender $sender) : bool {
       return $sender->hasPermission($this->permission);
    }

    abstract public function execute(CommandSender $sender, array $args) : void;
}