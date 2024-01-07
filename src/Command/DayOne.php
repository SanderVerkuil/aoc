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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contents = file_get_contents(__DIR__ . '/../../input/d1.txt');

        $lines = explode(PHP_EOL, $contents);

        $characters = array_map(
            static fn(string $line) => str_split($line),
            $lines
        );

        $integers = array_map(
            static fn(array $line) => array_filter(
                $line,
                static fn(string $char) => is_numeric($char)
            ),
            $characters
        );

        $first = static fn (array $element) => reset($element);
        $last = static fn(array $element) => array_pop($element);

        $coords = array_map(
            static fn($line) => [(int)$first($line), (int) $last($line)],
            $integers
        );

        dump(array_sum(
            array_map('array_sum', $coords)
        ));

        return self::SUCCESS;
    }
}
