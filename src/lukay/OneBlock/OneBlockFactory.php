<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use JsonException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class OneBlockFactory{
    use SingletonTrait;

    public array $loadedOneBlock = [];
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

        if(!$oneBlock instanceof OneBlock) return;

        $data = $this->getData();
        $data->set($player->getName(), json_encode($oneBlock->jsonSerialize()));
        $data->save();
    }

    public function loadData(Player $player) : void{
        $data = $this->getData();

        $encodedOneBlock = $data->get($player->getName());
        $decodedOneBlock = json_decode($encodedOneBlock, true);

        if(Server::getInstance()->getWorldManager()->getWorldByName($decodedOneBlock["name"]) === null) Server::getInstance()->getWorldManager()->loadWorld($decodedOneBlock["name"]);

        $this->loadedOneBlock[$player->getName()] = $this->fromJson($decodedOneBlock);
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
        if($this->getData()->exists($player->getName()) || isset($this->loadedOneBlock[$player->getName()])){
            return true;
        }
        return false;
    }

    public function fromJson(array $data) : OneBlock{
        return new OneBlock($data["owner"], $data["name"], $data["brokenSpawnerBlockCounter"], $data["phase"]);
    }
}