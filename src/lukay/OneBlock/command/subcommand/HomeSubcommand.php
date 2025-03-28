<?php

declare(strict_types=1);

namespace lukay\OneBlock\command\subcommand;

use lukay\OneBlock\OneBlockFactory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class HomeSubcommand extends Subcommand{

    public function execute(CommandSender $sender, array $args): void{
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;

        $oneBlockFactory = OneBlockFactory::getInstance();

        if(!$oneBlockFactory->hasOneBlock($sender)){
            $sender->sendMessage("§cYou do not own a OneBlock world");
            return;
        }

        $sender->teleport($oneBlockFactory->get($sender)->getWorld()->getSpawnLocation());
        $sender->sendPopup("§7» §aSuccessfully teleported to the OneBlock world");
    }
}