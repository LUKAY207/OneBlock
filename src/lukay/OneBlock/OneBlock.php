<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use lukay\OneBlock\generator\OneBlock as OneBlockGenerator;
use pocketmine\block\Block;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\utils\DirtType;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

class OneBlock{

    private ?Player $owner;
    private string $name;
    private World $world;
    private int $brokenSpawnerBlockCounter = 0;
    private int $phase = OneBlockFactory::PHASE_ONE;

    public function __construct(Player $owner, string $name){
        $this->owner = $owner;
        $this->name = $name;

        $server = Server::getInstance();
        $server->getWorldManager()->generateWorld($name,
            WorldCreationOptions::create()
                ->setSpawnPosition(new Vector3(0, 64, 0))
                ->setSeed(mt_rand())
                ->setGeneratorClass(OneBlockGenerator::class));
        $this->world = $server->getWorldManager()->getWorldByName($name);
    }

    public function getOwner() : Player{
        return $this->owner;
    }

    public function setOwner(Player $newOwner) : void{
        $this->owner = $newOwner;
    }

    public function getName() : string{
        return $this->name;
    }

    public function setName(string $newName) : void{
        $this->name = $newName;
    }

    public function getWorld() : World{
        return $this->world;
    }

    public function setWorld(World $newWorld) : void{
        $this->world = $newWorld;
    }

    public function isSpawnerBlock(Block $block) : bool{
        if($block->getPosition()->equals(new Vector3(0, 64, 0))){
            return true;
        }else{
            return false;
        }
    }

    public function getBrokenSpawnerBlocksCounter() : int{
        return $this->brokenSpawnerBlockCounter;
    }

    public function addToBrokenSpawnerBlocks(int $amount) : void{
        $this->brokenSpawnerBlockCounter = $this->brokenSpawnerBlockCounter + $amount;
    }

    public function getPhase() : int{
        return $this->phase;
    }

    public function setPhase(int $newPhase) : void{
        $this->phase = $newPhase;
    }

    public function getStageBlocks() : ?array{
        $stageOneBlocks = [VanillaBlocks::STONE(), VanillaBlocks::DIRT(), VanillaBlocks::COAL_ORE(), VanillaBlocks::IRON_ORE(), VanillaBlocks::GOLD_ORE(), VanillaBlocks::REDSTONE_ORE(), VanillaBlocks::LAPIS_LAZULI_ORE(), VanillaBlocks::OAK_LOG(), VanillaBlocks::CHEST()];
        $stageTwoBlocks = [VanillaBlocks::SNOW(), VanillaBlocks::ICE(), VanillaBlocks::PACKED_ICE(), VanillaBlocks::COBBLESTONE(), VanillaBlocks::SPRUCE_LOG(), VanillaBlocks::PODZOL(), VanillaBlocks::DIRT()->setDirtType(DirtType::COARSE), VanillaBlocks::SPRUCE_LEAVES(), VanillaBlocks::MOSSY_COBBLESTONE(), VanillaBlocks::CHEST()];

        if($this->getPhase() === OneBlockFactory::PHASE_ONE){
            return $stageOneBlocks;
        }elseif($this->getPhase() === OneBlockFactory::PHASE_TWO){
            return array_merge($stageOneBlocks, $stageTwoBlocks);
        }
        return null;
    }

    public function getNewBlock() : Block{
        $possibleBlocks = $this->getStageBlocks();
        return $possibleBlocks[array_rand($possibleBlocks)] ?? VanillaBlocks::GRASS();
    }
}