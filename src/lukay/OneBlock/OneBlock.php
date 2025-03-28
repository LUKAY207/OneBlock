<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use JsonSerializable;
use lukay\OneBlock\generator\OneBlock as OneBlockGenerator;
use pocketmine\block\Block;
use pocketmine\block\utils\DirtType;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

class OneBlock implements JsonSerializable{

    private readonly OneBlockFactory $oneBlockFactory;

    private string $owner;
    private string $name;
    private World $world;
    private int $brokenSpawnerBlockCounter;
    private int $phase;

    public function __construct(string $ownerName, string $name, int $brokenSpawnerBlockCounter = 0,  int $phase = 1){
        $this->oneBlockFactory = OneBlockFactory::getInstance();
        $this->owner = $ownerName;
        $this->name = $name;

        $server = Server::getInstance();

        if($server->getWorldManager()->getWorldByName($name) === null) {
            $server->getWorldManager()->generateWorld($name,
                WorldCreationOptions::create()
                    ->setSpawnPosition(new Vector3(0, 65, 0))
                    ->setSeed(mt_rand())
                    ->setGeneratorClass(OneBlockGenerator::class));
        }

        $this->world = $server->getWorldManager()->getWorldByName($name);
        $this->brokenSpawnerBlockCounter = $brokenSpawnerBlockCounter;
        $this->phase = $phase;
    }

    public function getOwner() : ?Player{
        if(Server::getInstance()->getPlayerExact($this->owner) === null) return null;
        return Server::getInstance()->getPlayerExact($this->owner);
    }

    public function setOwner(Player $newOwner) : void{
        $this->owner = $newOwner->getName();
        $this->oneBlockFactory->updateData($this);
    }

    public function getName() : string{
        return $this->name;
    }

    public function setName(string $newName) : void{
        $this->name = $newName;
        $this->oneBlockFactory->updateData($this);
    }

    public function getWorld() : World{
        return $this->world;
    }

    public function setWorld(World $newWorld) : void{
        $this->world = $newWorld;
        $this->oneBlockFactory->updateData($this);
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
        $this->oneBlockFactory->updateData($this);
    }

    public function getPhase() : int{
        return $this->phase;
    }

    public function setPhase(int $newPhase) : void{
        $this->phase = $newPhase;
        $this->oneBlockFactory->updateData($this);
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

    public function jsonSerialize() : array{
        return
            [
                "name" => $this->name,
                "owner" => $this->owner,
                "world" => $this->world->getFolderName(),
                "brokenSpawnerBlockCounter" => $this->brokenSpawnerBlockCounter,
                "phase" => $this->phase
            ];
    }
}