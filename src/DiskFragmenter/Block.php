<?php

namespace Sander\AdventOfCode\DiskFragmenter;

readonly class Block
{
    public function __construct(
        public ?int $blockId,
        public int  $size
    )
    {
    }
}