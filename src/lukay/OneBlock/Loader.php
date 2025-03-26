<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use JsonException;
use lukay\OneBlock\generator\OneBlock as OneBlockGenerator;
use lukay\OneBlock\listener\PlayerJoinListener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\generator\GeneratorManager;

class Loader extends PluginBase{
    use SingletonTrait;

    public function getConfig() : Config{
        return new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    protected function onLoad() : void{
        self::setInstance($this);
        $this->saveResource("config.yml");
    }

    protected function onEnable() : void{
        $generatorManager = GeneratorManager::getInstance();
        $generatorManager->addGenerator(OneBlockGenerator::class, "oneblock", fn() => null);

        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener(), $this);
    }

    /**
     * @throws JsonException
     */
    protected function onDisable() : void{
        OneBlockFactory::getInstance()->saveData();
    }

    public function usesMySQL() : bool{
        if($this->getConfig()->get("use-MySQL") !== "true"){
            return false;
        }
        return true;
    }
}