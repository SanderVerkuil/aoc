<?php

namespace Sander\AdventOfCode\GuardGallivant;

enum Direction: string
{
    case Up = '↑';
    case Left = '←';
    case Right = '→';
    case Down = '↓';

    public function turn(): self {
        $newDirection = match($this) {
            self::Up => self::Right,
            self::Right => self::Down,
            self::Down => self::Left,
            self::Left => self::Up,
        };
        logger()->info('Turning direction from {cur} to {tar}', ['cur' => $this->name, 'tar' => $newDirection->name]);

        return $newDirection;
    }

    public function relativeX(): int {
        return match($this) {
            self::Up, self::Down => 0,
            self::Left => -1,
            self::Right => 1,
        };
    }
    public function relativeY(): int {
        return match($this) {
            self::Left, self::Right => 0,
            self::Up => -1,
            self::Down => 1,
        };
    }

    public function horizontal(): bool
    {
        return in_array($this, [self::Left, self::Right]);
    }

    public function vertical(): bool
    {
        return in_array($this, [self::Up, self::Down]);
    }
}
