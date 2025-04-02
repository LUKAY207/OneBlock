<?php

declare(strict_types=1);

namespace lukay\OneBlock;

enum OneBlockPhase: int{
    case PLAINS = 1;
    case UNDERGROUND = 2;
    case DARK_FOREST = 3;
    case DEEP_OCEAN = 4;
    case OCEAN_MONUMENT = 5;
    case MUSHROOM_ISLAND = 6;
    case WARM_OCEAN = 7;
    case JUNGLE = 8;
    case BAMBOO_JUNGLE = 9;
    case TAIGA = 10;
    case FROZEN_OCEAN = 11;
    case SNOWY_TAIGA = 12;
    case SNOWY_PLAINS = 13;
    case BIRCH_FOREST = 14;
    case SWAMP = 15;
    case MOUNTAINS = 16;
    case SAVANNA = 17;
    case DESERT = 18;
    case BADLANDS = 19;
    case WOODED_BADLANDS = 20;
    case NETHER_WASTES = 21;
    case NETHER_FORTRESS = 22;
    case BASALT_DELTAS = 23;
    case SOUL_SAND_VALLEY = 24;
    case CRIMSON_FOREST = 25;
    case WARPED_FOREST = 26;
    case BASTION_REMNANT = 27;
    case IDYLL = 28;
    case STRONGHOLD = 29;
    case THE_END = 30;
}