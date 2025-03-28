<?php

declare(strict_types=1);

namespace lukay\OneBlock;

use JsonException;
use lukay\OneBlock\command\OneBlockCommand;
use lukay\OneBlock\generator\OneBlock as OneBlockGenerator;
use lukay\OneBlock\listener\BlockBreakListener;
use lukay\OneBlock\listener\PlayerJoinListener;
use lukay\OneBlock\listener\PlayerQuitListener;
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

        $listener =
            [
                new BlockBreakListener(),
                new PlayerJoinListener(),
                new PlayerQuitListener()
            ];

        $this->getServer()->getCommandMap()->register("OneBlock", new OneBlockCommand());

        foreach($listener as $listener_){
            $this->getServer()->getPluginManager()->registerEvents($listener_,  $this);
        }
    }

    /**
     * @throws JsonException
     */
    protected function onDisable() : void{
        foreach($this->getServer()->getOnlinePlayers() as $player) {
            OneBlockFactory::getInstance()->saveData($player);
        }
    }

    public function usesMySQL() : bool{
        if($this->getConfig()->get("use-MySQL") !== "true"){
            return false;
        }
        return true;
    }
}