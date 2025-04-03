<?php

declare(strict_types=1);

namespace lukay\OneBlock\listener;

use lukay\OneBlock\event\PhaseChangeEvent;
use lukay\OneBlock\event\SpawnerBlockBreakEvent;
use lukay\OneBlock\OneBlockFactory;
use lukay\OneBlock\OneBlockPhase;
use lukay\OneBlock\session\Session;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
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
        $newBlock = $oneBlock->getNewBlock();

        $oneBlock->getWorld()->setBlockAt(0, 64, 0, $newBlock);
        $oneBlock->addToBrokenSpawnerBlocks(1);

        static $phases = [
            1000 => OneBlockPhase::PLAINS,
            2000 => OneBlockPhase::UNDERGROUND,
            3000 => OneBlockPhase::DARK_FOREST,
            4000 => OneBlockPhase::DEEP_OCEAN,
            5000 => OneBlockPhase::OCEAN_MONUMENT,
            6000 => OneBlockPhase::MUSHROOM_ISLAND,
            7000 => OneBlockPhase::WARM_OCEAN,
            8000 => OneBlockPhase::JUNGLE,
            9000 => OneBlockPhase::BAMBOO_JUNGLE,
            10000 => OneBlockPhase::TAIGA,
            11000 => OneBlockPhase::FROZEN_OCEAN,
            12000 => OneBlockPhase::SNOWY_TAIGA,
            13000 => OneBlockPhase::SNOWY_PLAINS,
            14000 => OneBlockPhase::BIRCH_FOREST,
            15000 => OneBlockPhase::SWAMP,
            16000 => OneBlockPhase::MOUNTAINS,
            17000 => OneBlockPhase::SAVANNA,
            18000 => OneBlockPhase::DESERT,
            19000 => OneBlockPhase::BADLANDS,
            20000 => OneBlockPhase::WOODED_BADLANDS,
            21000 => OneBlockPhase::NETHER_WASTES,
            22000 => OneBlockPhase::NETHER_FORTRESS,
            23000 => OneBlockPhase::BASALT_DELTAS,
            24000 => OneBlockPhase::SOUL_SAND_VALLEY,
            25000 => OneBlockPhase::CRIMSON_FOREST,
            26000 => OneBlockPhase::WARPED_FOREST,
            27000 => OneBlockPhase::BASTION_REMNANT,
            28000 => OneBlockPhase::IDYLL,
            29000 => OneBlockPhase::STRONGHOLD,
            30000 => OneBlockPhase::THE_END
        ];

        if(isset($phases[$oneBlock->getBrokenSpawnerBlocksCounter()])){
            (new PhaseChangeEvent($oneBlock, $phases[$oneBlock->getBrokenSpawnerBlocksCounter()]))->call();
        }
    }
}