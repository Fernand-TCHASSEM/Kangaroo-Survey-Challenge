<?php

namespace App\Aggregation;

final class QcmAggregator implements AggregatorInterface
{
    public function supports(string $type): bool
    {
        return $type === 'qcm';
    }

    /**
     * @param  array<int, array<int, bool>>  $answers  one bool-per-option array per survey file
     * @param  array<int, string>|null  $options
     * @return array<string, int> option label => number of files where it was true
     */
    public function aggregate(array $answers, ?array $options): array
    {
        $counts = array_fill_keys($options ?? [], 0);

        foreach ($answers as $answer) {
            foreach ($answer as $index => $selected) {
                if ($selected && isset($options[$index])) {
                    $counts[$options[$index]]++;
                }
            }
        }

        return $counts;
    }
}
