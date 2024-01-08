<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    'aoc:day-two'
)]
class DayTwo extends Command
{

    /**
     * @param string $line
     * @return array
     */
    public function getGameAndDraws(string $line): array
    {
        preg_match(
            '/Game (\d+): ([\w ,;]+)/',
            $line,
            $matches
        );

        $game = $matches[1];

        $sets = explode('; ', $matches[2]);

        $draws = array_map(
            fn(string $set) => $this->getDraw(explode(', ', $set)),
            $sets
        );
        return array($game, $draws);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $games = explode(PHP_EOL, file_get_contents(__DIR__ . '/../../input/day-two/d2.txt'));

        $result = array_map(
            $this->isGamePossible(...),
            $games
        );

        $minSet = array_map(
            $this->getMinimumSet(...),
            $games
        );

        $powers  = array_map(
            fn(array $set) => $set['blue'] * $set['red'] * $set['green'],
            $minSet
        );

        $output->writeln(
            'The result is: ' . array_sum($result)
        );
        $output->writeln(
            'The result for part 2 is: ' . array_sum($powers)
        );

        return self::SUCCESS;
    }

    private function getMinimumSet(string $line): array
    {
        [$game, $draws] = $this->getGameAndDraws($line);

        return [
            'blue' => max(array_column($draws, 'blue')),
            'red' => max(array_column($draws, 'red')),
            'green' => max(array_column($draws, 'green')),
        ];
    }

    private function isGamePossible(string $line): int
    {
        $amounts = [
            'red' => 12,
            'green' => 13,
            'blue' => 14
        ];

        [$game, $draws] = $this->getGameAndDraws($line);

        $isDrawPossible = array_map(
            fn(array $draw) => $this->isDrawPossible($draw, $amounts),
            $draws
        );

        $possible = array_reduce(
            $isDrawPossible,
            static fn($carry, $output) => $carry && $output,
            true
        );

        if ($possible) {
            return $game;
        }
        return 0;
    }

    private function getDraw(array $amounts)
    {
        $totals = [
        ];

        foreach ($amounts as $amount) {
            preg_match(
                '/(\d+) (blue|red|green)/',
                $amount,
                $matches
            );
            $totals[$matches[2]] = (int)$matches[1];
        }

        return $totals;
    }

    private function isDrawPossible(array $draw, array $amounts): bool
    {
        foreach ($draw as $key => $amount) {
            if ($amount > $amounts[$key]) {
                return false;
            }
        }

        return true;
    }

}
