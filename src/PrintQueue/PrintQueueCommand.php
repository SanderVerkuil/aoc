<?php

namespace Sander\AdventOfCode\PrintQueue;

use Psr\Log\LoggerInterface;
use Sander\AdventOfCode\MullItOver\Mul;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:05', 'Print Queue')]
class PrintQueueCommand extends Command
{
    private LoggerInterface $logger;

    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger = new ConsoleLogger($output);
        $style = new SymfonyStyle($input, $output);
        $file = $input->getArgument('input');

        $fileContents = file_get_contents($file);

        $sum = $this->parseInput(new UnicodeString($fileContents));

        $style->success(
            'Got the result: ' . $sum,
        );

        return self::SUCCESS;
    }

    private function parseInput(UnicodeString $input): int
    {
        /** @var array<int, int[]> $rules */
        $rules = [];
        $firstSection = true;
        $sum = 0;
        foreach($input->split(PHP_EOL) as $line) {
            if ($line->isEmpty()) {
                $firstSection = false;
                continue;
            }

            if ($firstSection) {
                [$first, $second] = $line->split('|');
                if (!array_key_exists((int) $first->toString(), $rules)) {
                    $rules[(int) $first->toString()] = [];
                }
                $rules[(int) $first->toString()][] = (int) $second->toString();
            } else {
                $numbers = array_map(fn(UnicodeString $string) => (int) $string->toString(), $line->split(','));
                if($this->validateInput(
                    $numbers,
                    $rules,
                )) {
                    $sum += $this->middle($numbers);
                }
            }
        }

        return $sum;
    }

    /**
     * @param int[] $input
     * @param array<int, int[]> $rules
     * @return bool
     */
    private function validateInput(
        array $input,
        array $rules,
    ): bool {
        foreach($input as $index => $value) {
            foreach($rules as $before => $after) {
                if ($before === $value) {
                    continue;
                }
                $this->logger->info('Checking whether {value} is before {before}, using remaining numbers {after}', ['value' => $value, 'after' => implode(', ', array_slice($input, $index + 1)), 'before' => $before]);
                if (in_array($value, $after, true) && in_array($before, array_slice($input, $index + 1))) {
                    $this->logger->warning('The number {value} needs to be after number  {before}', ['value' => $value, 'before' => $before]);
                    return false;
                }
            }
        }

        return true;
    }

    private function middle(array $input): int
    {
        $index = count($input) / 2;

        return $input[(int) floor($index)];
    }
}