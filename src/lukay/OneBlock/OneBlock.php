<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use lukay\OneBlock\generator\OneBlock as OneBlockGenerator;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

class OneBlock{

    private ?Player $owner;
    private string $name;
    private World $world;
    private $brokenSpawnerBlockCounter = 0;

    public function __construct(Player $owner, string $name){
        $this->owner = $owner;
        $this->name = $name;

        $server = Server::getInstance();
        $server->getWorldManager()->generateWorld($name,
            WorldCreationOptions::create()
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
}