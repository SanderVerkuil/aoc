<?php

namespace Sander\AdventOfCode\HoofIt;

class Map
{
    public const DIRECTIONS = [
        [-1, 0],
        [1, 0],
        [0, -1],
        [0, 1],
    ];

    /** @var array<Cell> */
    public array $ending = [];

    /** @var array<Cell> */
    public array $beginning = [];

    /**
     * @param array<int, array<int, Cell>> $blocks
     */
    public function __construct(
        public array $blocks,
    )
    {
        foreach ($blocks as $row) {
            foreach ($row as $cell) {
                if ($cell->value === 9) {
                    $this->ending[] = $cell;
                }
                if ($cell->value === 0) {
                    $this->beginning[] = $cell;
                }
            }
        }
    }

    public function get(int $x, int $y): ?Cell
    {
        return $this->blocks[$y][$x] ?? null;
    }

    public function print(): array
    {
        $rows = [];

        foreach ($this->blocks as $blockRow) {
            $row = [];

            foreach ($blockRow as $cell) {
                $row[] = $cell->print();
            }

            $rows[] = implode('', $row);
        }

        return $rows;
    }

    public function compute(): int
    {
        return array_sum(
            array_map(
                $this->getScore(...),
                $this->beginning
            )
        );
    }

    public function getScore(Cell $cell): int
    {
        logger()->info('Computing the score of cell ({x}, {y})', ['x' => $cell->x, 'y' => $cell->y]);
        if ($cell->score !== null) {
            return $cell->score;
        }
        $score = 0;
        foreach(self::DIRECTIONS as [$dx, $dy]) {
            $neighbor = $this->get($cell->x + $dx, $cell->y + $dy);
            if ($neighbor === null) {
                continue;
            }

            if ($neighbor->value === $cell->value + 1) {
                $neighborScore = $this->getScore($neighbor);
                logger()->info('The score of cell ({x}, {y}) is {score}', ['x' => $cell->x, 'y' => $cell->y, 'score' => $neighborScore]);
                $score += $neighborScore;
            }
        }
        $cell->score = $score;
        return $score;
    }

    public function score(): int
    {
        return array_sum(
            array_map(
                fn(array $line) => array_sum(
                    array_map(
                        fn(Cell $cell) => $cell->value === 0 ? ($cell->score ?? 0) : 0,
                        $line,
                    )
                ),
                $this->blocks,
            )
        );
    }
}

