<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use JsonException;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class OneBlockFactory{
    use SingletonTrait;

    private array $loadedOneBlock = [];
    public const int STAGE_ONE = 1;
    public const array STAGE_ONE_BLOCKS =
        [
        ];
    public const int STAGE_TWO = 2;
    public const array STAGE_TWO_BLOCKS =
        [
        ];

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