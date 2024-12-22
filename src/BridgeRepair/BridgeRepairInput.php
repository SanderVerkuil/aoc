<?php

namespace Sander\AdventOfCode\BridgeRepair;

use Symfony\Component\String\AbstractString;

class BridgeRepairInput
{
    public function __construct(
        public int $total,
        public array $values,
    ) {
    }

    public static function create(AbstractString $input): self
    {
        [$total, $remaining] = $input->split(': ');

        $values = array_map(
            function(AbstractString $value) {
                return (int) $value->toString();
            },
            $remaining->split(' ')
        );

        return new self(
            (int) $total->toString(),
            $values
        );
    }

    public function isValid(): bool
    {
        try {
            $places = count($this->values) - 1;
            $variations = pow(count(BridgeRepairOperator::cases()), $places);

            logger()->info('Computing {variations} variations for {places} places', ['variations' => $variations, 'places' => $places]);

            for ($i = 0; $i < $variations; ++$i) {
                $variation = str_pad(base_convert($i, 10, count(BridgeRepairOperator::cases())), $places, '0', STR_PAD_LEFT);
                logger()->info('Variation {variation}', ['variation' => $variation]);
                $operators = array_fill(0, $places, BridgeRepairOperator::Add);
                for ($operator = 0; $operator < $places; ++$operator) {

                    $check = (int)($variation[$operator]);
                    logger()->info('Setting place {place} to value {check}', ['place' => $operator, 'check' => $check]);

                    $operators[$operator] = BridgeRepairOperator::cases()[$check];
                }
                if ($this->validate($operators) === $this->total) {
                    return true;
                };
            }

            return false;
        } catch (\Throwable $t) {
            logger()->error($t->getMessage());

        }
    }

    private function validate(array $operators): int
    {
        $values = $this->values;
        $initial = array_shift($values);

        return array_reduce(
            array_map(null, $values, $operators),
            fn(int $carry, array $data) => $data[1]->apply($carry, $data[0]),
            $initial,
        );
    }

    public function toString(): string
    {
        return sprintf(
            '%s: %s',
            $this->total,
            implode(' ', $this->values)
        );
    }
}