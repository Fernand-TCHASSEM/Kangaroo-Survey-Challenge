<?php

namespace App\Aggregation;

final class NumericAggregator implements AggregatorInterface
{
    public function supports(string $type): bool
    {
        return $type === 'numeric';
    }

    /**
     * @param  array<int, int|float>  $answers
     */
    public function aggregate(array $answers, ?array $options): float
    {
        return array_sum($answers) / count($answers);
    }
}
