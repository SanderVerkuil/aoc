<?php

namespace Sander\AdventOfCode;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:01', description: 'Historian Hysteria')]
class HistorianHysteriaCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
        $this->addOption('variant', mode: InputOption::VALUE_OPTIONAL, default: 'first', suggestedValues: ['first', 'second']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $file = $input->getArgument('input');

        $fileContents = file_get_contents($file);

        [$leftList, $rightList] = $this->parseInput(new UnicodeString($fileContents));

        if ($input->getOption('variant') === 'first') {
            $solution = $this->solveFirstHalf($leftList, $rightList);

            $style->success('Found a solution: ' . $solution);
        } elseif ($input->getOption('variant') === 'second') {
            $solution = $this->solveSecondHalf($leftList, $rightList);

            $style->success('Found a solution: ' . $solution);
        } else {
            $style->error('Unsupported variant');
            return self::INVALID;
        }

        return self::SUCCESS;
    }

    private function solveFirstHalf(array $leftList, array $rightList): int
    {
        sort($leftList);
        sort($rightList);

        $solution = array_sum(array_map(
            fn(int $leftValue, int $rightValue): int => abs($leftValue - $rightValue),
            $leftList,
            $rightList
        ));

        return $solution;
    }

    private function solveSecondHalf(array $leftList, array $rightList): int
    {
        return array_sum(array_map(
            fn(int $leftItem) => $leftItem * count(
                    array_filter(
                        $rightList,
                        fn(int $rightValue) => $leftItem === $rightValue
                    )
                ),
            $leftList
        ));
    }

    private function parseInput(AbstractString $input): array
    {
        $leftList = [];
        $rightList = [];

        $lines = $input->split(PHP_EOL);

        foreach ($lines as $line) {
            $inputs = $line->split('   ');
            $leftList[] = (int)$inputs[0]->toString();
            $rightList[] = (int)$inputs[1]->toString();
        }

        return [$leftList, $rightList];
    }
}