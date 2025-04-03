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
    private array $allowedPlayers = [];
    private OneBlockPhase $phase;

    public function __construct(string $ownerName, string $name, int $brokenSpawnerBlockCounter = 0, array $allowedPlayers = [], OneBlockPhase $phase = OneBlockPhase::PLAINS){
        $this->oneBlockFactory = OneBlockFactory::getInstance();
        $this->owner = $ownerName;
        $this->name = $name;
        $this->world = $this->initWorld($name);
        $this->brokenSpawnerBlockCounter = $brokenSpawnerBlockCounter;
        $this->allowedPlayers = $allowedPlayers;
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

    public function getAllowedPlayers() : array{
        return $this->allowedPlayers;
    }

    public function isAllowedPlayer(Player $player) : bool{
        return in_array($player->getName(), $this->allowedPlayers);
    }

    public function addAllowedPlayer(Player  $player) : void{
        $this->allowedPlayers[] = $player->getName();
    }

    public function getPhase() : OneBlockPhase{
        return $this->phase;
    }

    public function setPhase(OneBlockPhase $newPhase) : void{
        $this->phase = $newPhase;
        $this->oneBlockFactory->updateData($this);
    }

    public function getStageBlocks() : array{
        static $stages = [
            OneBlockPhase::PLAINS->value => [
                "minecraft:grass_block", "minecraft:dirt", "minecraft:oak_wood", "minecraft:oak_planks", "minecraft:oak_sapling", "minecraft:oak_leaves",
                "minecraft:cobblestone", "minecraft:stone", "minecraft:gravel", "minecraft:sand", "minecraft:poppy", "minecraft:blue_orchid"
            ],
            OneBlockPhase::UNDERGROUND->value => [
                "minecraft:stone", "minecraft:cobblestone", "minecraft:iron_ore", "minecraft:coal_ore", "minecraft:gold_ore",
                "minecraft:diamond_ore", "minecraft:lapis_ore", "minecraft:redstone_ore", "minecraft:mossy_cobblestone", "minecraft:gravel",
                "minecraft:dirt", "minecraft:oak_log", "minecraft:chest"
            ],
            OneBlockPhase::DARK_FOREST->value => [
                "minecraft:dark_oak_wood", "minecraft:dark_oak_planks", "minecraft:dark_oak_sapling", "minecraft:dark_oak_leaves",
                "minecraft:podzol", "minecraft:moss", "minecraft:mushroom_block", "minecraft:brown_mushroom", "minecraft:red_mushroom",
                "minecraft:vine", "minecraft:cobblestone", "minecraft:stone"
            ],
            OneBlockPhase::DEEP_OCEAN->value => [
                "minecraft:prismarine", "minecraft:sea_lantern", "minecraft:coral_block", "minecraft:kelp", "minecraft:sea_grass",
                "minecraft:sponge", "minecraft:clay", "minecraft:sandstone", "minecraft:sand", "minecraft:gravel"
            ],
            OneBlockPhase::OCEAN_MONUMENT->value => [
                "minecraft:prismarine", "minecraft:dark_prismarine", "minecraft:sea_lantern", "minecraft:sponge", "minecraft:stone",
                "minecraft:sand"
            ],
            OneBlockPhase::MUSHROOM_ISLAND->value => [
                "minecraft:mycelium", "minecraft:mushroom_block", "minecraft:red_mushroom", "minecraft:brown_mushroom",
                "minecraft:grass", "minecraft:podzol", "minecraft:cobblestone"
            ],
            OneBlockPhase::WARM_OCEAN->value => [
                "minecraft:coral_block", "minecraft:sea_lantern", "minecraft:kelp", "minecraft:sand", "minecraft:clay",
                "minecraft:prismarine", "minecraft:sandstone", "minecraft:water"
            ],
            OneBlockPhase::JUNGLE->value => [
                "minecraft:jungle_wood", "minecraft:jungle_planks", "minecraft:jungle_leaves", "minecraft:jungle_sapling",
                "minecraft:melon_block", "minecraft:bamboo", "minecraft:cocoa", "minecraft:podzol", "minecraft:grass",
                "minecraft:moss"
            ],
            OneBlockPhase::BAMBOO_JUNGLE->value => [
                "minecraft:bamboo", "minecraft:jungle_wood", "minecraft:jungle_leaves", "minecraft:jungle_sapling",
                "minecraft:melon_block", "minecraft:cocoa", "minecraft:podzol", "minecraft:grass", "minecraft:moss"
            ],
            OneBlockPhase::TAIGA->value => [
                "minecraft:spruce_wood", "minecraft:spruce_planks", "minecraft:spruce_leaves", "minecraft:spruce_sapling",
                "minecraft:snow", "minecraft:ice", "minecraft:podzol", "minecraft:moss", "minecraft:cobblestone", "minecraft:stone"
            ],
            OneBlockPhase::FROZEN_OCEAN->value => [
                "minecraft:ice", "minecraft:packed_ice", "minecraft:snow", "minecraft:prismarine", "minecraft:sand",
                "minecraft:sea_lantern"
            ],
            OneBlockPhase::SNOWY_TAIGA->value => [
                "minecraft:spruce_wood", "minecraft:spruce_planks", "minecraft:spruce_leaves", "minecraft:spruce_sapling",
                "minecraft:snow", "minecraft:ice", "minecraft:podzol", "minecraft:moss", "minecraft:cobblestone", "minecraft:snow_block"
            ],
            OneBlockPhase::SNOWY_PLAINS->value => [
                "minecraft:snow", "minecraft:snow_block", "minecraft:ice", "minecraft:packed_ice", "minecraft:stone",
                "minecraft:cobblestone", "minecraft:spruce_sapling", "minecraft:grass", "minecraft:oak_wood"
            ],
            OneBlockPhase::BIRCH_FOREST->value => [
                "minecraft:birch_wood", "minecraft:birch_planks", "minecraft:birch_leaves", "minecraft:birch_sapling",
                "minecraft:grass", "minecraft:flowers", "minecraft:moss", "minecraft:cobblestone"
            ],
            OneBlockPhase::SWAMP->value => [
                "minecraft:swamp_grass", "minecraft:water", "minecraft:slime_block", "minecraft:lily_pad", "minecraft:vine",
                "minecraft:mud", "minecraft:acacia_wood", "minecraft:birch_wood"
            ],
            OneBlockPhase::MOUNTAINS->value => [
                "minecraft:stone", "minecraft:granite", "minecraft:andesite", "minecraft:diorite", "minecraft:grass",
                "minecraft:snow", "minecraft:cobblestone", "minecraft:moss"
            ],
            OneBlockPhase::SAVANNA->value => [
                "minecraft:acacia_wood", "minecraft:acacia_planks", "minecraft:acacia_sapling", "minecraft:grass",
                "minecraft:dead_bush", "minecraft:tall_grass", "minecraft:sand", "minecraft:stone"
            ],
            OneBlockPhase::DESERT->value => [
                "minecraft:sand", "minecraft:red_sand", "minecraft:sandstone", "minecraft:cactus", "minecraft:dead_bush",
                "minecraft:stone", "minecraft:gravel"
            ],
            OneBlockPhase::BADLANDS->value => [
                "minecraft:red_sand", "minecraft:red_terracotta", "minecraft:smooth_red_terracotta", "minecraft:cactus",
                "minecraft:dead_bush", "minecraft:stone", "minecraft:clay"
            ],
            OneBlockPhase::WOODED_BADLANDS->value => [
                "minecraft:acacia_wood", "minecraft:red_sand", "minecraft:terracotta", "minecraft:red_terracotta",
                "minecraft:cactus", "minecraft:dead_bush", "minecraft:stone"
            ],
            OneBlockPhase::NETHER_WASTES->value => [
                "minecraft:netherrack", "minecraft:soul_sand", "minecraft:nether_quartz_ore", "minecraft:glowstone",
                "minecraft:basalt", "minecraft:crimson_nylium", "minecraft:lava"
            ],
            OneBlockPhase::NETHER_FORTRESS->value => [
                "minecraft:nether_brick", "minecraft:soul_sand", "minecraft:magma_block", "minecraft:stone_bricks"
            ],
            OneBlockPhase::BASALT_DELTAS->value => [
                "minecraft:basalt", "minecraft:polished_basalt", "minecraft:blackstone", "minecraft:magma_block",
                "minecraft:netherrack", "minecraft:lava"
            ],
            OneBlockPhase::SOUL_SAND_VALLEY->value => [
                "minecraft:soul_sand", "minecraft:soul_soil", "minecraft:nether_wart", "minecraft:bone_block",
                "minecraft:gravel", "minecraft:nether_quartz_ore"
            ],
            OneBlockPhase::CRIMSON_FOREST->value => [
                "minecraft:crimson_nylium", "minecraft:crimson_stem", "minecraft:warped_wart_block", "minecraft:shroomlight",
                "minecraft:polished_blackstone"
            ],
            OneBlockPhase::WARPED_FOREST->value => [
                "minecraft:warped_nylium", "minecraft:warped_stem", "minecraft:warped_wart_block", "minecraft:shroomlight",
                "minecraft:polished_blackstone"
            ],
            OneBlockPhase::BASTION_REMNANT->value => [
                "minecraft:polished_blackstone", "minecraft:soul_sand", "minecraft:magma_block", "minecraft:basalt",
                "minecraft:stone_bricks", "minecraft:nether_brick", "minecraft:gilded_blackstone"
            ],
            OneBlockPhase::IDYLL->value => [
                "minecraft:grass_block", "minecraft:dirt", "minecraft:oak_wood", "minecraft:oak_planks", "minecraft:oak_sapling",
                "minecraft:oak_leaves", "minecraft:water", "minecraft:flower", "minecraft:melons", "minecraft:wheat_block"
            ],
            OneBlockPhase::STRONGHOLD->value => [
                "minecraft:stone_bricks", "minecraft:mossy_stone_bricks", "minecraft:end_portal_frame", "minecraft:iron_bars",
                "minecraft:cobblestone", "minecraft:wooden_planks", "minecraft:gravel"
            ],
            OneBlockPhase::THE_END->value => [
                "minecraft:end_stone", "minecraft:end_stone_bricks", "minecraft:purpur_block", "minecraft:end_portal_frame",
                "minecraft:dragon_egg", "minecraft:chorus_flower", "minecraft:chorus_plant", "minecraft:obsidian",
                "minecraft:ender_chest"
            ]
        ];


        $phase = $this->getPhase()->value;

        $blocks = [];
        foreach (range(OneBlockPhase::PLAINS->value, $phase) as $currentPhase) {
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