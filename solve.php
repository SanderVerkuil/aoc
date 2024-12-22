#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Sander\AdventOfCode\CeresSearch\CeresSearchCommand;
use Sander\AdventOfCode\GuardGallivant\GuardGallivantCommand;
use Sander\AdventOfCode\HistorianHysteria\HistorianHysteriaCommand;
use Sander\AdventOfCode\Logger;
use Sander\AdventOfCode\MullItOver\MullItOverCommand;
use Sander\AdventOfCode\PrintQueue\PrintQueueCommand;
use Sander\AdventOfCode\RedNosedReports\RedNosedReportsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();

$dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event): void {
    Logger::set(new ConsoleLogger($event->getOutput()));
    Logger::get()->info('Set up the logger');
});

$application = new Application('Advent of Code 2024', '1.0.0');

$application->setDispatcher($dispatcher);

$application->add(new HistorianHysteriaCommand());
$application->add(new RedNosedReportsCommand());
$application->add(new MullItOverCommand());
$application->add(new CeresSearchCommand());
$application->add(new PrintQueueCommand());
$application->add(new GuardGallivantCommand());

$application->setDefaultCommand('aoc:01');

function logger(): LoggerInterface
{
    return Logger::get();
}

$application->run();