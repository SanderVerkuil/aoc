<?php

namespace Sander\AdventOfCode\GuardGallivant;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:06', 'Guard Gallivant')]
class GuardGallivantCommand extends Command
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
//
//        Guard::$cursor = new Cursor($output, $input);
//        Guard::$style = $style;
//        Guard::$output = $output;

        $map = Map::create(new UnicodeString($fileContents));

        $progress = new ProgressBar($style, $map->guard->computeSteps());

        Guard::$progress = $progress;
        $map = Map::create(new UnicodeString($fileContents));

        $map->guard->walk();

        $style->success(
            'Got the result: ' . $map->visited(),
        );

        return self::SUCCESS;
    }
}