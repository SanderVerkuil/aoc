<?php

namespace Sander\AdventOfCode\GuardGallivant;

use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Guard
{
    public static ProgressBar $progress;
    public ?Map $map = null;

    public function __construct(
        public int $x,
        public int $y,
        public Direction $direction,
    ) {
        logger()->info('Starting at {x}, {y}', ['x' => $this->x, 'y' => $this->y]);
    }

    public static ?OutputInterface $output = null;
    public static ?Cursor $cursor = null;
    public static ?SymfonyStyle $style = null;

    public function walk(): void
    {
        static::$progress->advance();

        logger()->info('Walking {direction}', ['direction' => $this->direction->name]);

        $facing = $this->map->get($this->x + $this->direction->relativeX(), $this->y + $this->direction->relativeY());

        $this->map->get($this->x, $this->y)->visit($this->direction);

        if ($facing === null) {
            return;
        }

        if ($facing->obstacle) {
            $this->direction = $this->direction->turn();
        } else {
            $this->display($this->map);
            if (($facing->x !== $this->map->startX || $facing->y !== $this->map->startY) && $facing->visited === 0) {
                $newGuard = new Guard($this->x, $this->y, $this->direction->turn());
                $newMap = new Map(
                    array_map(
                        fn(array $cells) => array_map(
                            fn(Cell $cell) => clone $cell,
                            $cells,
                        ),
                        $this->map->map
                    ),
                    $newGuard,
                    $this->map->width,
                    $this->map->height,
                    $this->map->startX,
                    $this->map->startY,
                );
                $newMap->get($facing->x, $facing->y)->obstacle = true;
                $newMap->get($facing->x, $facing->y)->markAsBlocker();
                $this->display($newMap);

                if ($newGuard->isInLoop()) {
                    $facing->markAsBlocker();;
                    $newMap->get($facing->x, $facing->y)->potentialBlocker = true;
                }
            }

            $this->x += $this->direction->relativeX();
            $this->y += $this->direction->relativeY();
        }

        $this->walk();
    }

    private function isInLoop(): bool
    {
        do {

            $facing = $this->map->get($this->x + $this->direction->relativeX(), $this->y + $this->direction->relativeY());

            $this->map->get($this->x, $this->y)->visit($this->direction);

            if ($facing === null) {
                return false;
            }

            if (in_array($this->direction->value, $facing->visitedDirections)) {
                return true;
            }

            if ($facing->obstacle) {
                $this->direction = $this->direction->turn();
            } else {
                $this->x += $this->direction->relativeX();
                $this->y += $this->direction->relativeY();
            }
        } while (true);
    }

    public function computeSteps(): int
    {
        $steps = 0;
        do {
            $steps++;
            $facing = $this->map->get($this->x + $this->direction->relativeX(), $this->y + $this->direction->relativeY());

            $this->map->get($this->x, $this->y)->visit($this->direction);

            if ($facing === null) {
                return $steps;
            }

            if (in_array($this->direction->value, $facing->visitedDirections)) {
                return $steps;
            }

            if ($facing->obstacle) {
                $this->direction = $this->direction->turn();
            } else {
                $this->x += $this->direction->relativeX();
                $this->y += $this->direction->relativeY();
            }
        } while (true);
    }

    /**
     * @param Map $newMap
     * @return void
     */
    public function display(Map $newMap): void
    {
        static::$cursor?->clearScreen();
        static::$cursor?->moveToPosition(1, 1);
        static::$output?->writeln($newMap->print());
        static::$cursor?->moveToPosition(1, 1 + $this->map->height);
        static::$style?->confirm('Computing a loop...');
    }
}
