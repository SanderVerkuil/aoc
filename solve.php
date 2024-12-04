#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use Sander\AdventOfCode\RedNosedReportsCommand;
use Symfony\Component\Console\Application;

$application = new Application('Advent of Code 2024', '1.0.0');
$dayOneCommand = new \Sander\AdventOfCode\HistorianHysteriaCommand();
$dayTwoCommand = new RedNosedReportsCommand();

$application->add($dayOneCommand);
$application->add($dayTwoCommand);

$application->setDefaultCommand('aoc:01');

$application->run();