<?php

namespace Sander\AdventOfCode\PlutonianPebbles;

class Pebble
{
    public function __construct(
        public int $value
    )
    {

    }

    public function blink(): array
    {
        if ($this->value === 0) {
            logger()->info('Matched first rule, returning one pebble of value 1');
            return [new Pebble(1)];
        }
        if (($numDigits = strlen((string)$this->value)) % 2 === 0) {
            [$firstList, $secondList] = array_chunk(str_split((string)$this->value), $numDigits / 2);
            $first = implode($firstList);
            $second = implode($secondList);
            logger()->info('Number of digits ({digits}) was even ({numDigits}), splitting into {first} and {second}', [
                'digits' => $this->value,
                'numDigits' => $numDigits,
                'first' => $first,
                'second' => $second
            ]);
            return [new Pebble((int)$first), new Pebble((int)$second)];
        }

        return [new Pebble($this->value * 2024)];
    }
}