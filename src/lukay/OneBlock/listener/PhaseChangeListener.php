<?php

declare(strict_types=1);

namespace lukay\OneBlock\listener;

use lukay\OneBlock\event\PhaseChangeEvent;
use pocketmine\event\Listener;

class PhaseChangeListener implements Listener{

    public function onChange(PhaseChangeEvent $event) : void{
        $oneBlock = $event->getOneBlock();
        $oneBlock->setPhase($event->getPhase());
    }
}