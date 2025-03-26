<?php

namespace lukay\OneBlock\session;

use lukay\OneBlock\OneBlockFactory;
use pocketmine\player\Player;
use WeakMap;

final class Session {

    private static WeakMap $data;
    private static Player $player;

    public static function get(Player $player): Session {
        self::$player = $player;
        self::$data ??= new WeakMap();
        return self::$data[$player] ??=  new Session();
    }

    public function getPlayer() : Player{
        return self::$player;
    }

    public function isInOneBlockWorld() : bool{
        $oneBlockFactory = OneBlockFactory::getInstance();

        if(self::$player->getWorld() === $oneBlockFactory->get(self::$player)->getWorld()){
            return true;
        }else{
            return false;
        }
    }
}