<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use lukay\OneBlock\generator\OneBlock as OneBlockGenerator;
use pocketmine\block\Block;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
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
    private int $stage = OneBlockFactory::STAGE_ONE;

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

    public function getStage() : int{
        return $this->stage;
    }

    public function setStage(int $newStage) : void{
        $this->stage = $newStage;
    }

    public function getStageBlocks() : ?array{
        $stageOneBlocks = [VanillaBlocks::GRASS(), VanillaBlocks::STONE(), VanillaBlocks::OAK_LOG()];
        $stageTwoBlocks = [];
        if($this->stage === OneBlockFactory::STAGE_ONE){
            return $stageOneBlocks;
        }elseif($this->stage === OneBlockFactory::STAGE_TWO){
            return array_merge($stageOneBlocks, $stageTwoBlocks);
        }
        return null;
    }

    public function getNewBlock() : Block{
        $possibleBlocks = $this->getStageBlocks() ?? null;
        $randomBlock = $possibleBlocks[array_rand($possibleBlocks)];

        if($randomBlock instanceof Block){
            return $randomBlock;
        }

        return VanillaBlocks::GRASS();
    }
}