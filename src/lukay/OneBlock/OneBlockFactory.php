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

    public function create(OneBlock $oneBlock) : void{
        $this->loadedOneBlock[$oneBlock->getOwner()->getName()] = $oneBlock;
    }

    public function get(Player $owner) : ?OneBlock{
        if(!isset($this->loadedOneBlock[$owner->getName()])){
            return null;
        }
        return $this->loadedOneBlock[$owner->getName()];
    }
}