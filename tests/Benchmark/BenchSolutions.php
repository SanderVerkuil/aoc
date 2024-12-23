<?php

namespace Sander\AdventOfCode\Tests\Benchmark;

use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;
use Sander\AdventOfCode\CeresSearch\CeresSearchCommand;
use Sander\AdventOfCode\HistorianHysteria\HistorianHysteriaCommand;
use Sander\AdventOfCode\MullItOver\MullItOverCommand;
use Sander\AdventOfCode\RedNosedReports\RedNosedReportsCommand;
use Symfony\Component\Console\Tester\CommandTester;

class BenchSolutions
{
    #[Revs(10)]
    #[Iterations(5)]
    public function benchRedNosedReports(): void
    {
        $command = new RedNosedReportsCommand();

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'input' => __DIR__ . '/../../days/02/input.txt'
        ]);
    }

    #[Revs(1000)]
    #[Iterations(5)]
    public function benchHistorianHysteria(): void
    {
        $command = new HistorianHysteriaCommand();

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'input' => __DIR__ . '/../../days/01/input_small.txt'
        ]);
    }

    #[Revs(10)]
    #[Iterations(5)]
    public function benchMullItOver(): void
    {
        $command = new MullItOverCommand();

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'input' => __DIR__ . '/../../days/03/input.txt'
        ]);
    }

    #[Revs(10)]
    #[Iterations(5)]
    public function benchCeresSearch(): void
    {
        $command = new CeresSearchCommand();

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'input' => __DIR__ . '/../../days/04/input.txt'
        ]);
    }
}