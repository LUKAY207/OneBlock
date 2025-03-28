<?php

declare(strict_types=1);

namespace lukay\OneBlock\listener;

use lukay\OneBlock\event\SpawnerBlockBreakEvent;
use lukay\OneBlock\OneBlockFactory;
use lukay\OneBlock\OneBlockPhase;
use lukay\OneBlock\session\Session;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\world\particle\BlockBreakParticle;

class BlockBreakListener implements Listener{

    public function onBreak(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        $session = Session::get($player);
        $block = $event->getBlock();

        if($session->isInOneBlockWorld()){
            $oneBlock = OneBlockFactory::getInstance()->get($player);
            if($oneBlock->isSpawnerBlock($block)){

                $vector3 = new Vector3(0, 65, 0);
                $world = $block->getPosition()->getWorld();

                $world->addParticle($vector3, new BlockBreakParticle($block));
                $world->dropExperience($vector3, $event->getXpDropAmount());

                foreach($event->getDrops() as $item) {
                    $world->dropItem($vector3, $item);
                }

                $event->cancel();
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
            $oneBlock->setPhase(OneBlockPhase::PHASE_TWO);
        }elseif($oneBlock->getBrokenSpawnerBlocksCounter() === 2000){
            $oneBlock->setPhase(OneBlockPhase::PHASE_THREE);
            //FURTHER PHASES...
        }
    }
}