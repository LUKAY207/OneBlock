<?php

declare(strict_types=1);

namespace lukay\OneBlock\command\subcommand;

use lukay\OneBlock\OneBlock;
use lukay\OneBlock\OneBlockFactory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class CreateSubcommand extends Subcommand{

    public function execute(CommandSender $sender, array $args) : void{
        if(!$sender instanceof Player){
            return;
        }

        if(!$this->testPermission($sender)){
            return;
        }

        $oneBlockFactory = OneBlockFactory::getInstance();
        if($oneBlockFactory->hasOneBlock($sender)){
            $sender->sendMessage("§cYou already own a OneBlock world. You are not allowed to create more than one");
            return;
        }

        if(count($args[0]) === 0){
            $sender->sendMessage("§cYou need to give a name for the OneBlock: §7/oneblock create name");
            return;
        }

        if(Server::getInstance()->getWorldManager()->getWorldByName($args[0]) !== null){
            $sender->sendMessage("§cYou cannot use that name.");
            return;
        }

        $oneBlockFactory->create(new OneBlock($sender, $args[0]));
        $oneBlock = $oneBlockFactory->get($sender);

        if(!$oneBlock->getWorld()->isLoaded()){
            Server::getInstance()->getWorldManager()->loadWorld($oneBlock->getWorld()->getFolderName());
        }
        $sender->teleport(new Position(0,64, 0, $oneBlock->getWorld()));
    }
}