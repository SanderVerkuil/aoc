<?php

namespace Sander\AdventOfCode\ResonantCollinearity;

use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

class Map
{
    public function __construct(
        public int   $width,
        public int   $height,
        /**
         * @var array<int, array<int, Cell>>
         */
        public array $map,
        /**
         * @var array<string, Cell[]>
         */
        public array $antennas,
    )
    {
    }

    /**
     * @param AbstractString[] $lines
     * @return self
     */
    public static function create(array $lines): self
    {
        $map = [];
        $height = count($lines);
        $width = $lines[0]->length();

        $antennas = [];

        for ($y = 0; $y < $height; $y++) {
            $map[$y] = [];
            for ($x = 0; $x < $width; $x++) {
                $frequency = $lines[$y]->slice($x, 1);
                if ($frequency->toString() === '.') {
                    $frequency = null;
                }
                $cell = new Cell($x, $y, $frequency);
                if ($frequency !== null) {
                    if (!array_key_exists($frequency->toString(), $antennas)) {
                        $antennas[$frequency->toString()] = [];
                    }
                    $antennas[$frequency->toString()][] = $cell;
                }
                $map[$y][$x] = $cell;
            }
        }

        return new self(
            $width,
            $height,
            $map,
            $antennas,
        );
    }

    public function get(int $x, int $y): ?Cell
    {
        return $this->map[$y][$x] ?? null;
    }

    public function inBounds(int $x, int $y): bool
    {
        return !($x < 0 || $y < 0 || $x >= $this->width || $y >= $this->height);
    }

    public function toString(): AbstractString
    {
        $result = array_map(
            function ($cells) {
                return implode('', array_map(static fn(Cell $cell) => $cell->toString(), $cells));
            },
            $this->map
        );

        return new UnicodeString(implode(PHP_EOL, $result));
    }

    public function findAntinodes(): array
    {
        $antinodes = [];
        foreach($this->antennas as $frequency => $antennas) {
            $antinodes[$frequency] = [];

            for ($i = 0; $i < count($antennas) - 1; $i++) {
                $antenna = $antennas[$i];
                $remaining = array_slice($antennas, $i + 1);

                logger()->info('Checking antenna {antenna} against {antennas}', ['antenna' => $antenna, 'antennas' => implode(' ', $remaining)]);

                foreach($remaining as $other) {
                    foreach($this->computeAntiNodes($antenna, $other) as $node) {
                        logger()->info('Found antinode at {x}, {y}', ['x' => $node->x, 'y' => $node->y]);
                    }
                }
            }
        }

        return [];
    }

    /**
     * @param Cell $antenna
     * @param Cell $other
     * @return Cell[]
     */
    private function computeAntinodes(Cell $antenna, Cell $other): array
    {
        $dx = $antenna->x - $other->x;
        $dy = $antenna->y - $other->y;

        $first = $this->get($antenna->x + $dx, $antenna->y + $dy);
        $second = $this->get($other->x - $dx, $other->y - $dy);

        logger()->info('Difference between nodes was ({dx}, {dy}), found two positions: ({fx}, {fy}), ({sx}, {sy})', [
            'dx' => $dx,
            'dy' => $dy,
            'fx' => $first?->x,
            'fy' => $first?->y,
            'sx' => $second?->x,
            'sy' => $second?->y,
        ]);

        $first?->antinode($antenna->frequency);
        $second?->antinode($antenna->frequency);

        return array_filter([$first, $second]);
    }

    public function countAntinodes(): int {
        return array_sum(
            array_map(
                fn(array $line) => array_sum(
                    array_map(
                        static fn(Cell $cell) => count($cell->antinodes) > 0 ? 1 : 0,
                        $line,
                    ),
                ),
                $this->map,
            )
        );
    }
}