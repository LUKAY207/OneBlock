<?php

declare(strict_types=1);

namespace lukay\OneBlock\generator;

use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

class OneBlock extends Generator{

    public function __construct(int $seed, string $preset){
        parent::__construct($seed, $preset);
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void{
        if($chunkX === 16 && $chunkZ === 16){
            $world->getChunk($chunkX, $chunkZ)->setBlockStateId(0, 64, 0, VanillaBlocks::GRASS()->getStateId());
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void{
    }
}