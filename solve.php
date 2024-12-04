#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use Sander\AdventOfCode\HistorianHysteria\HistorianHysteriaCommand;
use Sander\AdventOfCode\MullItOver\MullItOverCommand;
use Sander\AdventOfCode\RedNosedReports\RedNosedReportsCommand;
use Symfony\Component\Console\Application;

$application = new Application('Advent of Code 2024', '1.0.0');
$dayOneCommand = new HistorianHysteriaCommand();
$dayTwoCommand = new RedNosedReportsCommand();
$dayThreeCommand = new MullItOverCommand();

$application->add($dayOneCommand);
$application->add($dayTwoCommand);
$application->add($dayThreeCommand);

$application->setDefaultCommand('aoc:01');

$application->run();