<?php

namespace Sander\AdventOfCode\GuardGallivant;

use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\UnicodeString;

class Map
{
    /**
     * @param array<int, array<int, Cell> $map
     */
    public function __construct(
        public array $map,
        public Guard $guard,
        public int $width,
        public int $height,
        public int $startX,
        public int $startY,
    ) {
        $guard->map = $this;
    }

    public static function create(UnicodeString $input): self
    {
        $guardPosition = null;
        $map = [];
        $height = count($input->split(PHP_EOL));
        $guardDirection = Direction::Up;
        foreach($input->split(PHP_EOL) as $y => $line) {
            $width = strlen($line);
            foreach($line->chunk() as $x => $value) {
                if (in_array($value->toString(), ['<', '^', '>'], true)){
                    $guardDirection = match($value->toString()) {
                        '<' => Direction::Left,
                        '>' => Direction::Right,
                        '^' => Direction::Up,
                    };
                    $guardPosition = [$x, $y];
                    $map[$y][$x] = new Cell($x, $y, 0, false);
                } else {
                    $map[$y][$x] = match ($value->toString()) {
                        '.', '#' => new Cell($x, $y, 0, $value->toString() === '#'),
                    };
                }

            }
        }

        if ($guardPosition === null) {
            throw new \RuntimeException('No guard found');
        }

        $guard = new Guard($guardPosition[0], $guardPosition[1], $guardDirection);

        return new Map(
            $map,
            $guard,
            $width,
            $height,
            $guard->x,
            $guard->y,
        );
    }

    public function get(int $x, int $y): ?Cell {
        return $this->map[$y][$x] ?? null;
    }

    public function visited(): int
    {
        return array_sum(
            array_map(
                fn(array $line) => array_sum(
                    array_map(
                        fn(Cell $cell) => $cell->potentialBlocker ? 1 : 0,
                        $line
                    )
                ),
                $this->map
            )
        );
    }

    public function print(): array
    {
        $messages = [];
        foreach($this->map as $line) {
            $message = '';
            /** @var Cell $cell */
            foreach($line as $cell) {
                $message .= $cell->print();
            }
            $messages[] = $message;
        }
        return $messages;
    }
}