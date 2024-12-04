<?php

namespace Sander\AdventOfCode;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'aoc:02', description: 'Red Nosed Reports')]
class RedNosedReportsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hi!');

        return Command::SUCCESS;
    }
}