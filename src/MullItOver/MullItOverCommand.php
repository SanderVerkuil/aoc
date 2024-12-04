<?php

namespace Sander\AdventOfCode\MullItOver;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\UnicodeString;

#[AsCommand('aoc:03', 'Mull It Over')]
class MullItOverCommand extends Command
{
    private readonly LoggerInterface $logger;

    protected function configure(): void
    {
        $this->addArgument('input', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger = new ConsoleLogger($output);
        $style = new SymfonyStyle($input, $output);
        $file = $input->getArgument('input');

        $fileContents = file_get_contents($file);

        $commands = $this->parseInput(new UnicodeString($fileContents));

        dump(['Found commands' => $commands]);

        $style->success(
            'Got the result: ' . array_sum(array_map(fn(Mul $command) => $command->value(), $commands))
        );

        return self::SUCCESS;
    }

    private function parseInput(UnicodeString $input): array
    {
        $commands = [];
        $currentTarget = null;
        $mulEnabled = true;
        $currentCommand = null;
        $firstDigit = null;
        $secondDigit = null;
        $pointer = 0;
        while ($pointer < strlen($input)) {
            $currentCharacter = $input->toString()[$pointer];
            $this->logger->info('Target: {currentTarget}, command: {currentCommand}, firstDigit: {firstDigit}, secondDigit: {secondDigit}', compact('currentTarget', 'currentCommand', 'firstDigit', 'secondDigit'));
            $this->logger->info('Current character {character}', ['character' => $currentCharacter]);
            switch($currentTarget) {
                case null:
                    if (substr($input->toString(), $pointer, strlen('do()')) === 'do()') {
                        $this->logger->info('Enabling mul');
                        $mulEnabled = true;
                        $pointer += strlen('do()') - 1;
                        break;
                    }
                    if (substr($input->toString(), $pointer, strlen('don\'t()')) === 'don\'t()') {
                        $this->logger->info('Disabling mul');
                        $mulEnabled = false;
                        $pointer += strlen('don\'t()') - 1;
                        break;
                    }
                    $this->logger->info('Checking for mul(');
                    if (substr($input->toString(), $pointer, 4) === 'mul(') {
                        $this->logger->info('Found mul!');
                        $pointer += 3;

                        if ($mulEnabled) {
                            $currentCommand = 'mul';
                            $currentTarget = 'firstDigit';
                        }
                    }
                    break;
                case 'firstDigit':
                    if (is_numeric($currentCharacter)) {
                        if (strlen($firstDigit) === 3) {
                            $this->logger->warning('The digit {digit} was too long, resetting', ['digit' => $firstDigit]);
                            $currentTarget = null;
                            $currentCommand = null;
                            $firstDigit = null;
                            break;
                        }
                        $firstDigit = ($firstDigit ?? '') . $currentCharacter;
                        $this->logger->info('Found another digit! {digit}', ['digit' => $firstDigit]);
                        break;
                    }
                    if ($currentCharacter === ',') {
                        $this->logger->info('Found the comma, moving to the second digit. First digit was {digit}', ['digit' => $firstDigit]);
                        $currentTarget = 'secondDigit';
                        break;
                    }
                    $this->logger->warning('Invalid character found, resetting');
                    $currentTarget = null;
                    $currentCommand = null;
                    $firstDigit = null;
                    break;
                case 'secondDigit':
                    if (is_numeric($currentCharacter)) {
                        if (strlen($secondDigit) === 3) {
                            $this->logger->warning('The digit {digit} was too long, resetting', ['digit' => $secondDigit]);
                            $currentTarget = null;
                            $currentCommand = null;
                            $firstDigit = null;
                            $secondDigit = null;
                            break;
                        }
                        $secondDigit = ($secondDigit ?? '') . $currentCharacter;
                        $this->logger->info('Found another digit! {digit}', ['digit' => $secondDigit]);
                        break;
                    }
                    if ($currentCharacter === ')') {
                        $this->logger->info('Found the end of the mul. Second digit was {digit}', ['digit' => $secondDigit]);
                        $commands[] = new Mul((int) $firstDigit, (int) $secondDigit);
                        $currentTarget = null;
                        $currentCommand = null;
                        $firstDigit = null;
                        $secondDigit = null;
                        break;
                    }
                    $this->logger->warning('Invalid character found, resetting');
                    $currentTarget = null;
                    $currentCommand = null;
                    $firstDigit = null;
                    $secondDigit = null;

            }
            ++$pointer;
        }

        return $commands;
    }
}