<?php

declare(strict_types=1);

namespace lukay\OneBlock\event;

use lukay\OneBlock\OneBlock;
use lukay\OneBlock\OneBlockPhase;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

class PhaseChangeEvent extends Event implements Cancellable{
    use CancellableTrait;

    public function __construct(
        private readonly OneBlock $oneBlock,
        private OneBlockPhase $phase
    ){
        $this->eventName = "PhaseChangeEvent";
    }

    public function getOneBlock() : OneBlock{
        return $this->oneBlock;
    }

    public function getPhase() : OneBlockPhase{
        return $this->phase;
    }

    public function setPhase(OneBlockPhase $phase) : void{
        $this->phase = $phase;
    }
}