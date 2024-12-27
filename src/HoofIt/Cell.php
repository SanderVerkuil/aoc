<?php

namespace Sander\AdventOfCode\HoofIt;

use Symfony\Component\Console\Color;

class Cell
{
    public function __construct(
        public int  $x,
        public int  $y,
        public int  $value,
        public ?int $score = null,
    ) {
        if ($this->value === 9) {
            $this->score = 1;
        }
    }

    public function print(): string
    {
        $color = new Color(
            match ($this->value) {
                0 => '#1bd0c7',
                1 => '#00c9d4',
                2 => '#00bedb',
                3 => '#00b4e0',
                4 => '#00abe5',
                5 => '#00a1ec',
                6 => '#0096fa',
                7 => '#0885ff',
                8 => '#636dff',
                9 => '#944dff',
            },
            $this->score !== null && $this->score > 0 ? 'blue' : 'red'
        );

        return $color->apply((string)$this->value);
    }
}
