<?php

namespace Sander\AdventOfCode\BridgeRepair;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

#[AsCommand(name: 'aoc:07', description: 'Bridge Repair')]
class BridgeRepairCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $style->title('Bridge Repair');
        $file = $input->getArgument('input');

        $fileContents = new UnicodeString(file_get_contents($file));

        $lines = $fileContents->split(PHP_EOL);
        $progress = $style->createProgressBar(count($lines));
        $progress->setFormat('debug');
        $result = $this->handleInput($lines, $progress);

        $style->writeln(['', '']);

        $style->success($result);

        return self::SUCCESS;
    }

    private function handleInput(array $strings, ?ProgressBar $progressBar): int
    {
        return array_sum(
            array_map(
                fn(BridgeRepairInput $input) => $input->total,
                array_filter(
                    array_map(
                        BridgeRepairInput::create(...),
                        $strings,
                    ),
                    function(BridgeRepairInput $input) use ($progressBar) {
                        $progressBar?->advance();
                        return $input->isValid();
                    },
                ),
            )
        );
    }
}