<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use JsonException;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class OneBlockFactory{
    use SingletonTrait;

    private array $loadedOneBlock = [];
    public const PHASE_ONE = 1;
    public const PHASE_TWO = 2;
    public const PHASE_THREE = 3;
    public const PHASE_FOUR = 4;
    public const PHASE_FIVE = 5;
    public const PHASE_SIX = 6;
    public const PHASE_SEVEN = 7;
    public const PHASE_EIGHT = 8;
    public const PHASE_NINE = 9;
    public const PHASE_TEN = 10;
    public const PHASE_ELEVEN = 11;
    public const PHASE_TWELVE = 12;
    public const PHASE_THIRTEEN = 13;
    public const PHASE_FOURTEEN = 14;
    public const PHASE_FIFTEEN = 15;
    public const PHASE_SIXTEEN = 16;

    public function getData() : Config{
        return new Config(Loader::getInstance()->getDataFolder() . "data.json", Config::JSON);
    }

    /**
     * @throws JsonException
     */
    public function saveData(Player $player) : void{
        $oneBlock = $this->loadedOneBlock[$player->getName()];

        $data = $this->getData();
        $data->set($player->getName(), json_encode($oneBlock));
        $data->save();
    }

    public function loadData(Player $player) : void{
        $data = $this->getData();

        $encodedOneBlock = $data->get($player->getName());
        $decodedOneBlock = json_decode($encodedOneBlock);

        $this->loadedOneBlock[$player->getName()] = $decodedOneBlock;
    }

    /**
     * @throws JsonException
     */
    public function unloadData(Player $player) : void{
        $this->saveData($player);
        unset($this->loadedOneBlock[$player->getName()]);
    }

    public function updateData(OneBlock $oneBlock) : void{
        $this->loadedOneBlock[$oneBlock->getOwner()->getName()] = $oneBlock;
    }

    public function create(OneBlock $oneBlock) : void{
        $this->loadedOneBlock[$oneBlock->getOwner()->getName()] = $oneBlock;
    }

    public function get(Player $owner) : ?OneBlock{
        if(!isset($this->loadedOneBlock[$owner->getName()])){
            return null;
        }
        return $this->loadedOneBlock[$owner->getName()];
    }

    public function hasOneBlock(Player $player) : bool{
        if(isset($this->loadedOneBlock[$player->getName()])){
            return true;
        }
        return false;
    }
}