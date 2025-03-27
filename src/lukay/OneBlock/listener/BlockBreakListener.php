<?php

declare(strict_types=1);

namespace lukay\OneBlock\listener;

use lukay\OneBlock\event\SpawnerBlockBreakEvent;
use lukay\OneBlock\OneBlockFactory;
use lukay\OneBlock\session\Session;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Server;
use pocketmine\world\Position;

class BlockBreakListener implements Listener{

    public function onBreak(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        $session = Session::get($player);

        if($session->isInOneBlockWorld()){
            $oneBlock = OneBlockFactory::getInstance()->get($player);
            if($oneBlock->isSpawnerBlock($event->getBlock())){
                (new SpawnerBlockBreakEvent($oneBlock))->call();
            }
        }
    }

    public function onSpawnerBlockBreak(SpawnerBlockBreakEvent $event) : void{
        if($event->isCancelled()) return;

        $oneBlock = $event->getOneBlock();
        $newBlock = $oneBlock->getNewBlock();

        $oneBlock->getWorld()->setBlockAt(0, 64, 0, $newBlock);
        $oneBlock->addToBrokenSpawnerBlocks(1);

        if($oneBlock->getBrokenSpawnerBlocksCounter() === 1000){
            $oneBlock->setPhase(OneBlockFactory::PHASE_TWO);
        }elseif($oneBlock->getBrokenSpawnerBlocksCounter() === 2000){
            $oneBlock->setPhase(OneBlockFactory::PHASE_TWO);
            //FURTHER PHASES...
        }
    }
}