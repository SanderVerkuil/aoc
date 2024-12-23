<?php

namespace Sander\AdventOfCode\DiskFragmenter;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:09', 'DiskFragmenter')]
class DiskFragmenter extends Command
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

        $size = count($map);

        $style->progressStart($size);

        for ($i = $size-1; $i >= 0; $i--) {
            $style->progressAdvance();
            $idx = array_find_key($map, fn(?int $blockId) => $blockId === null);
            if ($idx >= $i) {
                break;
            }

            $tmp = $map[$i];
            $map[$i] = $map[$idx];
            $map[$idx] = $tmp;
        }

        $checksum = 0;
        foreach($map as $index => $blockId) {
            if ($blockId === null) {
                break;
            }
            $checksum += $index * $blockId;
        }

        $style->success('Found the result: ' . $checksum);

        return Command::SUCCESS;
    }

    /**
     * @param AbstractString $input
     * @return int[]
     */
    private function parseInput(AbstractString $input): array
    {
        $blocks = array_map(
            function (AbstractString $blockSize, int $index): Block {
                $blockId = (int)$index / 2;
                $size = (int)$blockSize->toString();

                return $index % 2 === 0 ? new Block($blockId, $size) : new Block(null, $size);
            },
            $input->chunk(),
            range(0, $input->length() - 1),
        );

        return array_merge(
            ...array_map(
                fn(Block $block) => array_fill(0, $block->size, $block->blockId),
                $blocks,
            )
        );
    }
}
