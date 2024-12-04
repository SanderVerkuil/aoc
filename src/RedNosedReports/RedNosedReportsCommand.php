<?php

namespace Sander\AdventOfCode\RedNosedReports;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

#[AsCommand(name: 'aoc:02', description: 'Red Nosed Reports')]
class RedNosedReportsCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
        $this->addOption('max-errors', mode: InputOption::VALUE_OPTIONAL, default: '1', suggestedValues: ['1', '2']);
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);
        $style = new SymfonyStyle($input, $output);
        $file = $input->getArgument('input');

        $fileContents = file_get_contents($file);

        $reports = $this->parseInput(new UnicodeString($fileContents), $logger);

        $maxErrors = (int) $input->getOption('max-errors');

        $countValid = count(
            array_filter(
                $reports,
                fn(RedNosedReport $report) => $report->isValid($maxErrors),
            )
        );

        $style->success('Found the result: ' . $countValid);

        return Command::SUCCESS;
    }

    private function parseInput(AbstractString $input, ConsoleLogger $logger): array
    {
        $reports = array_map(
            fn(AbstractString $report) => RedNosedReport::create($report->split(' '), $logger),
            $input->split(PHP_EOL),
        );

        return $reports;
    }
}