<?php

namespace Sander\AdventOfCode\PlutonianPebbles;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:11', description: 'Plutonian Pebbles')]
class PlutonianPebblesCommand extends Command
{
    private array $cache = [];

    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
        $this->addOption('blinks', mode: InputOption::VALUE_OPTIONAL, description: 'The amount of blinks', default: 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $file = $input->getArgument('input');

        $fileContents = file_get_contents($file);

        $pebbles = $this->parseInput(new UnicodeString($fileContents));

        $blinks = (int) $input->getOption('blinks');

        $result = $this->countPebblesAfterBlink($pebbles, $blinks);

        $style->success('After ' . $blinks . ' blinks, there are ' . $result . ' pebbles');

        return self::SUCCESS;
    }

    private function parseInput(AbstractString $input): array
    {
        return array_map(
            fn(AbstractString $string): Pebble => new Pebble((int) $string->toString()),
            $input->split(' '),
        );
    }

    private function countPebblesAfterBlink(array $pebbles, int $blinkAmount): int
    {
        return array_sum(
            array_map(
                fn (Pebble $pebble) => $this->countPebbleAfterBlink($pebble, $blinkAmount),
                $pebbles,
            )
        );
    }

    private function countPebbleAfterBlink(Pebble $pebble, int $blinkAmount): int
    {
        $key = sprintf('%d__%d', $pebble->value, $blinkAmount);
        if (!array_key_exists($key, $this->cache)) {
            logger()->info('Checking amount of pebbles for pebble {pebble}', ['pebble' => $pebble->value]);

            if ($blinkAmount <= 0) {
                $this->cache[$key] = 1;
            } else {
                $this->cache[$key] =  array_sum(
                    array_map(
                        fn(Pebble $pebble) => $this->countPebbleAfterBlink($pebble, $blinkAmount - 1),
                        $pebble->blink(),
                    )
                );
            }
        } else {
            logger()->info('Hit cache! {key}, {value}', ['key' => $key, 'value' => $this->cache[$key]]);
        }
        return $this->cache[$key];
    }
}

