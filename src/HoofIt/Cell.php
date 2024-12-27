<?php

namespace Sander\AdventOfCode\HoofIt;

use Symfony\Component\Console\Color;

class Cell
{
    public function __construct(
        public int   $x,
        public int   $y,
        public int   $value,
        public array $reachableNines = [],
    )
    {
        if ($this->value === 9) {
            $this->reachableNines = [$this];
        }
    }

    public function addReachableNines(Cell ...$nines): void
    {
        foreach ($nines as $nine) {
            $this->addReachableNine($nine);
        }
    }

    public function addReachableNine(Cell $nine): void
    {
        if (!in_array($nine, $this->reachableNines)) {
            $this->reachableNines[] = $nine;
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
            count($this->reachableNines) > 0 ? 'blue' : 'red'
        );

        return $color->apply((string)$this->value);
    }

    public function reaches(array $reachableNines): bool
    {
        return array_any($reachableNines, fn($nine) => !in_array($nine, $this->reachableNines));
    }
}
