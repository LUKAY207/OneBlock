<?php

declare(strict_types=1);

namespace lukay\OneBlock\event;

use lukay\OneBlock\OneBlock;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class SpawnerBlockBreakEvent extends Event implements Cancellable {
    use CancellableTrait;

    public function __construct(
    private readonly OneBlock $oneBlock){
        $this->eventName = "SpawnerBlockBreakEvent";
    }

    public function getOneBlock() : OneBlock{
        return $this->oneBlock;
    }
}