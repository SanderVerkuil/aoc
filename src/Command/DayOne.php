<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    'aoc:day-one'
)]
class DayOne extends Command
{
    private const MAPPING = [
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
    ];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contents = file_get_contents(__DIR__ . '/../../input/d1.txt');

        $lines = explode(PHP_EOL, $contents);

        $first = fn (string $element) => $this->firstElement($element);
        $last = fn(string $element) => $this->lastElement($element);

        $coords = array_map(
            static fn($line) => [(int)$first($line), (int) $last($line)],
            $lines
        );

        $sum = array_sum(
            array_map(
                static fn(array $coord) => (int) ($coord[0] . $coord[1]),
                $coords
            )
        );

        $output->write('The sum is: ' . $sum . PHP_EOL);

        return self::SUCCESS;
    }

    private function firstElement(string $line)
    {
        $index = 0;
        do {
            $character = $line[$index++];

            if (is_numeric($character)) {
                return $character;
            }

            foreach(self::MAPPING as $number => $value) {
                if (str_starts_with($value, $character)) {
                    if (substr($line, $index-1, strlen($value)) === $value) {
                        return $number;
                    }
                }
            }
        } while ($index < strlen($line));
    }
    private function lastElement(string $line)
    {
        $index = strlen($line);
        do {
            $character = $line[--$index];
            if (is_numeric($character)) {
                return $character;
            }

            foreach(self::MAPPING as $number => $value) {
                if (str_ends_with($value, $character)) {
                    if (substr($line, $index+1-strlen($value), strlen($value)) === $value) {
                        return $number;
                    }
                }
            }

        } while ($index > 0);
    }
}
