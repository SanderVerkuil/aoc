<?php

namespace Sander\AdventOfCode\CeresSearch;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:04', 'Ceres Search')]
class CeresSearchCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
        $this->addOption('variant', mode: InputOption::VALUE_OPTIONAL, default: 'xmas', suggestedValues: ['xmas', 'x-mas']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $style->title('Ceres Search');
        $file = $input->getArgument('input');

        $fileContents = file_get_contents($file);

        if ($input->getOption('variant') === 'xmas') {
            $board = new WordSearchBoard(new UnicodeString($fileContents)->split(PHP_EOL));

            $style->success('XMAS occurs ' . $board->count() . ' times');
        } else {
            $board = new MasWordSearchBoard(new UnicodeString($fileContents)->split(PHP_EOL));

            $style->success('X-Mas occurs ' . $board->count() . ' times');
        }

        return self::SUCCESS;
    }
}