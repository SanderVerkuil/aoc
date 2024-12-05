<?php

namespace Sander\AdventOfCode\CeresSearch;

class MasWordSearchBoard
{
    private const DIRECTIONS = [
        [[-1, -1], [0, 0], [1, 1]],
        [[-1, 1], [0, 0], [1, -1]],
    ];

    private readonly int $height;
    private readonly int $width;

    public function __construct(
        private readonly array $lines,
    ) {
        $this->height = count($this->lines);
        $this->width = max(array_map('strlen', $this->lines));
    }

    public function wordsAt(int $column, int $line): array
    {
        $result = [
            0 => '',
            1 => '',
        ];

        foreach(self::DIRECTIONS as $index => $directionIndices) {
            foreach($directionIndices as $direction) {
                $result[$index] .= $this->characterAt($column + $direction[0], $line + $direction[1]);
            }
        }

        return $result;
    }

    public function characterAt(int $column, int $line): string
    {
        if (!array_key_exists($line, $this->lines)) {
            return ' ';
        }
        return $this->lines[$line]->toString()[$column] ?? ' ';
    }

    public function count(): int
    {
        $count = 0;
        for ($i = 1; $i < $this->height - 1; ++$i) {
            for ($j = 1; $j < $this->width - 1; ++$j) {
                if (array_all(
                    $this->wordsAt($i, $j),
                    fn(string $word) => $word === 'MAS' || $word == 'SAM',
                )) {
                    ++$count;
                }
            }
        }

        return $count;
    }
}