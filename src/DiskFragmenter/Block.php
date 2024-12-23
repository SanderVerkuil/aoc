<?php

namespace Sander\AdventOfCode\DiskFragmenter;

readonly class Block
{
    public function __construct(
        public ?int $blockId,
        public int  $size
    ) {
    }

    public static function toString(
        Block $block,
    ) {
        return str_repeat($block->blockId ?? '.', $block->size);
    }
}