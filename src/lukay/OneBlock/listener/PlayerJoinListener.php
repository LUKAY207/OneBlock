<?php

declare(strict_types=1);

namespace lukay\OneBlock\listener;

use lukay\OneBlock\OneBlockFactory;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerJoinListener implements Listener{

    public function onJoin(PlayerJoinEvent $event) : void{
        $oneBlockFactory = OneBlockFactory::getInstance();

        if($oneBlockFactory->hasOneBlock($event->getPlayer())){
            $oneBlockFactory->loadData($event->getPlayer());
        }
    }
}