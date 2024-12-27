<?php

namespace Sander\AdventOfCode\HoofIt;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:10', 'Hoof It')]
class HoofItCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $file = $input->getArgument('input');

        $fileContents = file_get_contents($file);

        $map = $this->parseInput(new UnicodeString($fileContents));

        $map->compute();

        $score = $map->score();

        $style->writeln($map->print());

        $style->success('Found a trail score of ' . $score);

        return self::SUCCESS;
    }

    private function parseInput(UnicodeString $input): Map
    {
        $map = [];
        foreach($input->split(PHP_EOL) as $y =>  $line) {
            $row = [];
            foreach($line->chunk() as $x => $cell) {
                $row[$x] = new Cell(
                    $x, $y, (int) $cell->toString(),
                );
            }

            $map[$y] = $row;
        }

        return new Map($map);
    }
}