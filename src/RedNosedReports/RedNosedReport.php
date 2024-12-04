<?php

namespace Sander\AdventOfCode\RedNosedReports;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\String\AbstractString;

class RedNosedReport
{
    private function __construct(
        private(set) array $list = [],
        private LoggerInterface $logger,
    )
    {
    }

    /**
     * @param array<array-key, AbstractString> $list
     * @return self
     */
    public static function create(array $list, ConsoleLogger $logger): self
    {
        return new self(array_map(fn($item) => (int)$item->toString(), $list), $logger);
    }

    public function isValid(int $maxErrors = 1): bool
    {
        $direction = 0;
        $lastNumber = null;
        foreach ($this->list as $number) {
            if ($lastNumber === null) {
                $lastNumber = $number;
                continue;
            }
            if ($lastNumber === $number) {
                $this->logger->info('The number is the same {lastNumber} == {number}', compact('lastNumber', 'number'));
                if ($maxErrors <= 1) {
                    return false;
                }
                return array_any(
                    $this->alternatives(),
                    fn(RedNosedReport $report): bool => $report->isValid($maxErrors - 1)
                );
            }
            if ($direction === 0) {
                if ($number < $lastNumber) {
                    $direction = 1;
                } else if ($number > $lastNumber) {
                    $direction = -1;
                }
            }
            $diff = abs($lastNumber - $number);
            if ($diff < 1 || $diff > 3) {
                $this->logger->info('The difference was too big {lastNumber} - {number} is {diff}', compact('lastNumber', 'number', 'diff'));
                if ($maxErrors <= 1) {
                    return false;
                }
                return array_any(
                    $this->alternatives(),
                    fn(RedNosedReport $report): bool => $report->isValid($maxErrors - 1)
                );
            }
            if ($direction === 1 && $number >= $lastNumber) {
                $this->logger->info('It was the wrong direction {lastNumber} < {number}', compact('direction', 'lastNumber', 'number'));
                if ($maxErrors <= 1) {
                    return false;
                }
                return array_any(
                    $this->alternatives(),
                    fn(RedNosedReport $report): bool => $report->isValid($maxErrors - 1)
                );
            }
            if ($direction === -1 && $number <= $lastNumber) {
                $this->logger->info('It was the wrong direction {lastNumber} > {number}', compact('direction', 'lastNumber', 'number'));
                if ($maxErrors <= 1) {
                    return false;
                }
                return array_any(
                    $this->alternatives(),
                    fn(RedNosedReport $report): bool => $report->isValid($maxErrors - 1)
                );
            }
            $lastNumber = $number;
        }

        return true;
    }

    private function alternatives(): array
    {
        $result = [];

        foreach ($this->list as $index => $number) {
            $newList = $this->list;
            unset($newList[$index]);

            $result[] = new self($newList, $this->logger);
        }

        return $result;
    }
}