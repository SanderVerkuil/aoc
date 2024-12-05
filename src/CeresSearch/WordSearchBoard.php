<?php

namespace Sander\AdventOfCode\CeresSearch;

class WordSearchBoard
{
    private const DIRECTIONS = [
        [1, 0],
        [0, 1],
        [1, -1],
        [1, 1]
    ];

    private readonly int $height;
    private readonly int $width;
    private readonly array $directions;

    public function __construct(
        private readonly array $lines,
        private readonly string $search = 'XMAS',
    ) {
        $this->height = count($this->lines);
        $this->width = max(array_map('strlen', $this->lines));

        $directions = [
            0 => [[0, 0]],
            1 => [[0, 0]],
            2 => [[0, 0]],
            3 => [[0, 0]],
        ];

        foreach(self::DIRECTIONS as $index => $direction) {
            for($i = 1; $i < strlen($this->search); ++$i) {
                $directions[$index][] = [$directions[$index][$i-1][0] + $direction[0], $directions[$index][$i-1][1] + $direction[1]];
            }
        }

        $this->directions = $directions;
    }

    public function wordsAt(int $column, int $line): array
    {
        $result = [
            0 => '',
            1 => '',
            2 => '',
            3 => ''
        ];

        foreach($this->directions as $index => $directionIndices) {
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
        for ($i = 0; $i < $this->height; ++$i) {
            for ($j = 0; $j < $this->width; ++$j) {
                foreach($this->wordsAt($i, $j) as $word) {
                    if ($word === 'XMAS' || $word === 'SAMX') {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }
}