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

        if (!$session->isInOneBlockWorld()) return;

        $oneBlock = OneBlockFactory::getInstance()->get($player);
        $block = $event->getBlock();

        if (!$oneBlock->isSpawnerBlock($block)) return;

        $world = $block->getPosition()->getWorld();
        $vector3 = OneBlockFactory::$DROPS_VECTOR;
        $world->addParticle($vector3, new BlockBreakParticle($block));
        $xpAmount = $event->getXpDropAmount();

        if($xpAmount > 0) $world->dropExperience($vector3, $xpAmount);

        foreach ($event->getDrops() as $item) {
            $world->dropItem($vector3, $item);
        }

        $event->cancel();
        (new SpawnerBlockBreakEvent($oneBlock))->call();
    }

    public function onSpawnerBlockBreak(SpawnerBlockBreakEvent $event) : void{
        if($event->isCancelled()) return;

        $oneBlock = $event->getOneBlock();

        $oneBlock->getWorld()->setBlockAt(0, 64, 0, $oneBlock->getNewBlock());
        $oneBlock->addToBrokenSpawnerBlocks(1);


        static $phases = [
            1000 => OneBlockPhase::PHASE_TWO,
            2000 => OneBlockPhase::PHASE_THREE,
            // Add further phases here...
        ];

        if(isset($phases[$oneBlock->getBrokenSpawnerBlocksCounter()])){
            $oneBlock->setPhase($phases[$oneBlock->getBrokenSpawnerBlocksCounter()]);
        }
    }
}