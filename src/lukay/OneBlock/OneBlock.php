<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use JsonSerializable;
use lukay\OneBlock\generator\OneBlock as OneBlockGenerator;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\StringToItemParser;
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
    private OneBlockPhase $phase;

    public function __construct(string $ownerName, string $name, int $brokenSpawnerBlockCounter = 0, OneBlockPhase $phase = OneBlockPhase::PHASE_ONE){
        $this->oneBlockFactory = OneBlockFactory::getInstance();
        $this->owner = $ownerName;
        $this->name = $name;
        $this->world = $this->initWorld($name);
        $this->brokenSpawnerBlockCounter = $brokenSpawnerBlockCounter;
        $this->phase = $phase;
    }

    private function initWorld(string $name) : World{
        $server = Server::getInstance();
        $worldManager = $server->getWorldManager();
        $world = $worldManager->getWorldByName($name);

        if($world === null) {
            $worldManager->generateWorld($name,
                WorldCreationOptions::create()
                    ->setSpawnPosition(new Vector3(0, 65, 0))
                    ->setSeed(mt_rand())
                    ->setGeneratorClass(OneBlockGenerator::class));
            $world = $worldManager->getWorldByName($name);
        }
        return $world;
    }

    public function getOwner() : ?Player{
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
        return $block->getPosition()->equals(OneBlockFactory::$BLOCK_SPAWNER_VECTOR);
    }

    public function getBrokenSpawnerBlocksCounter() : int{
        return $this->brokenSpawnerBlockCounter;
    }

    public function addToBrokenSpawnerBlocks(int $amount) : void{
        $this->brokenSpawnerBlockCounter += $amount;
        $this->oneBlockFactory->updateData($this);
    }

    public function getPhase() : OneBlockPhase{
        return $this->phase;
    }

    public function setPhase(OneBlockPhase $newPhase) : void{
        $this->phase = $newPhase;
        $this->oneBlockFactory->updateData($this);
    }

    public function getStageBlocks() : array{
        static $stages =
            [
                OneBlockPhase::PHASE_ONE->value => ["minecraft:stone", "minecraft:dirt", "minecraft:coal_ore", "minecraft:iron_ore", "minecraft:gold_ore", "minecraft:redstone_ore", "minecraft:lapis_ore", "minecraft:oak_log", "minecraft:chest"],
                OneBlockPhase::PHASE_TWO->value => ["minecraft:snow", "minecraft_ice", "minecraft:packed_ice", "minecraft:cobblestone", "minecraft:spruce_log", "minecraft:podzol", "minecraft:coarse_dirt", "minecraft:spruce_leaves", "minecraft:mossy_cobblestone"]
            ];

        $phase = $this->getPhase()->value;

        $blocks = [];
        foreach (range(OneBlockPhase::PHASE_ONE->value, $phase) as $currentPhase) {
            if (isset($stages[$currentPhase])) {
                $blocks = array_merge($blocks, $stages[$currentPhase]);
            }
        }
        return $blocks;
    }

    public function getNewBlock() : Block{
        $possibleBlocks = $this->getStageBlocks();
        $blockString = $possibleBlocks[array_rand($possibleBlocks)];

        return StringToItemParser::getInstance()->parse($blockString)?->getBlock() ?? VanillaBlocks::GRASS();
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