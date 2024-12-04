<?php

namespace Sander\AdventOfCode\MullItOver;

class Mul
{
    public function __construct(
        public int $firstValue,
        public int $secondValue,
    ) {
    }

    public function value(): int
    {
        return $this->firstValue * $this->secondValue;
    }
}
