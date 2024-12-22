<?php

namespace Sander\AdventOfCode\BridgeRepair;

enum BridgeRepairOperator
{
    case Add;
    case Multiply;
    case Concatenate;

    public function apply(int $left, int $right): int
    {
        return match ($this) {
            self::Add => $left + $right,
            self::Multiply => $left * $right,
            self::Concatenate => (int) ($left . $right),
        };
    }
}