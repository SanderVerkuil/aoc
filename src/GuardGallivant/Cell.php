<?php

namespace Sander\AdventOfCode\GuardGallivant;

use Symfony\Component\Console\Color;

class Cell
{
    public function __construct(
        public int $x,
        public int $y,
        public int $visited,
        public bool $obstacle,
        public array $visitedDirections = [],
        public bool $potentialBlocker = false,
    ) {
    }

    public function visit(Direction $direction): void
    {
        logger()->info('Marking cell ({x}, {y}) as visited with direction {direction}, already visited with: [{directions}]', [
            'x' => $this->x,
            'y' => $this->y,
            'direction' => $direction->value,
            'directions' => implode(', ', $this->visitedDirections),
        ]);

        $this->visitedDirections[] = $direction->value;
        $this->visited ++;
    }

    public function print(): string
    {
        if ($this->potentialBlocker) {
            return new Color('green')->apply($this->getCharacter());
        }
        if ($this->obstacle) {
            return new Color('yellow')->apply($this->getCharacter());
        }
        return $this->getCharacter();
    }

    public function markAsBlocker(): void
    {
        $this->potentialBlocker = true;
    }

    /**
     * @return string
     */
    public function getCharacter(): string
    {
        if ($this->obstacle) {
            return '#';
        }
        $vertical = in_array(Direction::Up->value, $this->visitedDirections, true) || in_array(Direction::Down->value, $this->visitedDirections, true);
        $horizontal = in_array(Direction::Left->value, $this->visitedDirections, true) || in_array(Direction::Right->value, $this->visitedDirections, true);

        if ($vertical && $horizontal) {
            return '+';
        }
        if ($vertical) {
            return '|';
        }
        if ($horizontal) {
            return '-';
        }
        return '.';
    }
}