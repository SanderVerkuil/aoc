<?php

namespace Sander\AdventOfCode\DiskFragmenter;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:09', 'DiskFragmenter')]
class DiskFragmenter extends Command
{
    protected SymfonyStyle $style;
    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
        $this->addOption('per-file', mode: InputOption::VALUE_NEGATABLE, description: 'Whether to move per file or not');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $this->style = $style;
        $file = $input->getArgument('input');

        $fileContents = file_get_contents($file);

        $blocks = $this->parseInput(new UnicodeString($fileContents));

        if ($input->getOption('per-file')) {
            $defragmented = $this->defragmentPerFile($blocks);

            $checksum = $this->computeChecksum($this->toDescriptor($defragmented));

        } else {
            $defragmented = $this->defragmentPerBlock($this->toDescriptor($blocks));

            $checksum = $this->computeChecksum($defragmented);
        }

        $style->success('Found the result: ' . $checksum);

        return Command::SUCCESS;
    }

    /**
     * @param Block[] $blocks
     * @return array
     */
    private function defragmentPerFile(array $blocks): array
    {
        $blocks = $this->mergeEmptyBlocks(array_values($blocks));

        $this->style->progressStart(count($blocks));

        for ($i = count($blocks) - 1; $i >= 0; --$i) {
            $this->style->progressAdvance();
            $lastBlock = $blocks[$i];
            if ($lastBlock->blockId === null) {
                continue;
            }

            $index = array_find_key($blocks, fn(Block $block) => $block->blockId === null && $block->size >= $lastBlock->size);

            if ($index === null || $index >= $i) {
                continue;
            }

            $blocks = array_merge(
                array_slice($blocks, 0, $index),
                [$lastBlock, new Block(null, $blocks[$index]->size - $lastBlock->size)],
                array_map(fn(Block $block) => $block->blockId !== $lastBlock->blockId ? $block : new Block(null, $block->size), array_slice($blocks, $index + 1)),
            );
        }

        return $blocks;
    }

    /**
     * @param Block[] $blocks
     * @return Block[]
     */
    private function mergeEmptyBlocks(array $blocks): array
    {
        $newBlocks = [];
        for ($i = 0; $i < count($blocks); $i++) {
            $firstBlock = $blocks[$i];
            $secondBlock = $blocks[$i + 1] ?? null;

            if ($secondBlock === null) {
                $newBlocks[] = $firstBlock;
                continue;
            }

            if ($firstBlock->blockId === null && $secondBlock->blockId === null) {
                $i += 1;
                $newBlocks[] = new Block(null, $firstBlock->size + $secondBlock->size);
            } else {
                $newBlocks[] = $firstBlock;
            }
        }
        return $newBlocks;
    }

    /**
     * @param AbstractString $input
     * @return Block[]
     */
    private function parseInput(AbstractString $input): array
    {
        return array_map(
            function (AbstractString $blockSize, int $index): Block {
                $blockId = (int)$index / 2;
                $size = (int)$blockSize->toString();

                return $index % 2 === 0 ? new Block($blockId, $size) : new Block(null, $size);
            },
            $input->chunk(),
            range(0, $input->length() - 1),
        );
    }

    /**
     * @param array $blocks
     * @return array
     */
    private function toDescriptor(array $blocks): array
    {
        return array_merge(
            ...array_map(
                fn(Block $block) => array_fill(0, $block->size, $block->blockId),
                $blocks,
            )
        );
    }

    /**
     * @param int[] $map
     * @return int[]
     */
    private function defragmentPerBlock(array $map): array
    {
        $size = count($map);
        for ($i = $size - 1; $i >= 0; $i--) {
            $idx = array_find_key($map, fn(?int $blockId) => $blockId === null);
            if ($idx >= $i) {
                break;
            }

            $tmp = $map[$i];
            $map[$i] = $map[$idx];
            $map[$idx] = $tmp;
        }
        return $map;
    }

    /**
     * @param array $map
     * @return float|int
     */
    private function computeChecksum(array $map): int|float
    {
        $checksum = 0;
        foreach ($map as $index => $blockId) {
            if ($blockId === null) {
                continue;
            }
            $checksum += $index * $blockId;
        }
        return $checksum;
    }
}
