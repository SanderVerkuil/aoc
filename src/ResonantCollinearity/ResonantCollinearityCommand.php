<?php

namespace Sander\AdventOfCode\ResonantCollinearity;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:08', 'Resonant Collinearity')]
class ResonantCollinearityCommand extends Command
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

        $map->findAntinodes();

        $result = $map->countAntinodes();

        $output->writeln($map->toString());

        $style->success('Found the result: ' . $result);

        return Command::SUCCESS;
    }

    private function parseInput(AbstractString $input): Map
    {
        return Map::create($input->split(PHP_EOL));
    }
}