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

    public function compute(): void
    {
        /** @var \SplQueue<Cell> $queue */
        $queue = new \SplQueue();

        foreach ($this->ending as $block) {
            $queue->enqueue($block);
        }

        $queue->setIteratorMode(\SplQueue::IT_MODE_DELETE);

        foreach ($queue as $cell) {
            logger()->info('Computing cell at {x}, {y}', ['x' => $cell->x, 'y' => $cell->y]);

            foreach ($this->print() as $line) {
                logger()->debug($line);
            }

            foreach (self::DIRECTIONS as [$dx, $dy]) {
                $neighbor = $this->get(
                    $cell->x + $dx,
                    $cell->y + $dy,
                );
                if ($neighbor !== null && $neighbor->value === $cell->value - 1 && $neighbor->reaches($cell->reachableNines)) {
                    $neighbor->addReachableNines(...$cell->reachableNines);
                    $queue[] = $neighbor;
                }
            }
            if ($cell->value === 0) {
                logger()->info('Found a trail start at ({x}, {y}), reachable nodes was: {reachable}', ['x' => $cell->x, 'y' => $cell->y, 'reachable' => count($cell->reachableNines)]);
            }
        }
    }

    public function score(): int
    {
        return array_sum(
            array_map(
                fn(array $line) => array_sum(
                    array_map(
                        fn(Cell $cell) => $cell->value === 0 ? count($cell->reachableNines) : 0,
                        $line,
                    )
                ),
                $this->blocks,
            )
        );
    }
}

