<?php

declare(strict_types=1);

namespace lukay\OneBlock\listener;

use JsonException;
use lukay\OneBlock\OneBlockFactory;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerQuitListener implements Listener{

    /**
     * @throws JsonException
     */
    public function onQuit(PlayerQuitEvent $event) : void{
        $oneBlockFactory = OneBlockFactory::getInstance();

        if($oneBlockFactory->hasOneBlock($event->getPlayer())) {
            $oneBlockFactory->saveData($event->getPlayer());
            $oneBlockFactory->unloadData($event->getPlayer());
        }
    }
}