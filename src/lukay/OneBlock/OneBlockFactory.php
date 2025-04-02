<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use InvalidArgumentException;
use JsonException;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use RuntimeException;

class OneBlockFactory{
    use SingletonTrait;

    public array $loadedOneBlock = [];
    public static Vector3 $BLOCK_SPAWNER_VECTOR;
    public static Vector3 $DROPS_VECTOR;

    public function initPositions() : void{
        self::$BLOCK_SPAWNER_VECTOR = new Vector3(0, 64, 0);
        self::$DROPS_VECTOR = new Vector3(0, 65, 0);
    }

    public function getData() : Config{
        return new Config(Loader::getInstance()->getDataFolder() . "data.json", Config::JSON);
    }

    /**
     * @throws JsonException
     */
    public function saveData(Player $player) : void{
        $oneBlock = $this->loadedOneBlock[$player->getName()] ?? null;

        if(!$oneBlock instanceof OneBlock) return;

        $data = $this->getData();
        $data->set($player->getName(), json_encode($oneBlock->jsonSerialize()));
        $data->save();
    }

    public function loadData(Player $player) : void{
        $data = $this->getData();
        $encodedOneBlock = $data->get($player->getName());
        $decodedOneBlock = json_decode($encodedOneBlock, true);

        if($decodedOneBlock === null) throw new RuntimeException("Failed to decode OneBlock data for player" . $player->getName() . ".");

        $worldName = $decodedOneBlock["name"];
        $worldManager = Server::getInstance()->getWorldManager();

        if($worldManager->getWorldByName($worldName) === null) $worldManager->loadWorld($worldName);

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
        return isset($this->loadedOneBlock[$player->getName()]) || $this->getData()->exists($player->getName());
    }

    public function fromJson(array $data) : OneBlock{
        if(!isset($data["phase"]) || !OneBlockPhase::tryFrom($data["phase"])) throw new InvalidArgumentException("Invalid phase data");

        return new OneBlock($data["owner"], $data["name"], $data["brokenSpawnerBlockCounter"], OneBlockPhase::from($data["phase"]));
    }
}