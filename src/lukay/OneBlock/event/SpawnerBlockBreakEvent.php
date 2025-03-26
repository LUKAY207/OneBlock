<?php

declare(strict_types=1);

namespace lukay\OneBlock\event;

use lukay\OneBlock\OneBlock;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;
use pocketmine\player\Player;

class SpawnerBlockBreakEvent extends BlockBreakEvent implements Cancellable {
    use CancellableTrait;

    public function __construct(Player $player, Block $block, Item $item,
    private readonly OneBlock $oneBlock){
        parent::__construct($player, $block, $item);
        $this->eventName = "SpawnerBlockBreakEvent";
    }

    public function getOneBlock() : OneBlock{
        return $this->oneBlock;
    }
}