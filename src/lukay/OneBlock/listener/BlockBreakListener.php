<?php

declare(strict_types=1);

namespace lukay\OneBlock\listener;

use lukay\OneBlock\event\SpawnerBlockBreakEvent;
use lukay\OneBlock\OneBlockFactory;
use lukay\OneBlock\session\Session;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

class BlockBreakListener implements Listener{

    public function onBreak(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        $session = Session::get($player);

        if($session->isInOneBlockWorld()){
            $oneBlock = OneBlockFactory::getInstance()->get($player);
            if($oneBlock->isSpawnerBlock($event->getBlock())){
                (new SpawnerBlockBreakEvent($player, $event->getBlock(), $event->getItem(), $oneBlock))->call();
            }
        }
    }

    public function onSpawnerBlockBreak(SpawnerBlockBreakEvent $event) : void{
        if($event->isCancelled()) return;
        $oneBlock = $event->getOneBlock();
        $blockPosition = $event->getBlock()->getPosition();
        // Randomizer needs to be added
        $oneBlock->getWorld()->setBlockAt($blockPosition->getX(), $blockPosition->getY(), $blockPosition->getZ(), VanillaBlocks::TNT());
        $oneBlock->addToBrokenSpawnerBlocks(1);
    }
}