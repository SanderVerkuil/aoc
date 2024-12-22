<?php

namespace Sander\AdventOfCode\ResonantCollinearity;

use Symfony\Component\Console\Color;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

class Cell
{
    public function __construct(
        public int $x,
        public int $y,
        public ?string $frequency,
        public array $antinodes = [],
    ) {
    }

    public function toString(): AbstractString
    {
        $freq = $this->frequency ?? (count($this->antinodes) > 0 ? '#' : '.');

        if (count($this->antinodes) > 0) {
            $freq = new Color('red')->apply($freq);
        }

        return new UnicodeString($freq);
    }

    public function antinode(string $frequency): void
    {
        if (!in_array($frequency, $this->antinodes)) {
            $this->antinodes[] = $frequency;
        }
    }

    public function __toString(): string
    {
        return sprintf(
            '%s (%d, %d)',
            $this->frequency ?? (count($this->antinodes) > 0 ? '#' : '.'),
            $this->x,
            $this->y
        );
    }
}