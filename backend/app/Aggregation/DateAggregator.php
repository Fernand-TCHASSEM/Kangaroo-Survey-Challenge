<?php

namespace App\Aggregation;

/**
 * Bonus aggregator: no "date" question exists in the current data, this class
 * only demonstrates that a new question type can be added by dropping in one
 * class and registering it in AppServiceProvider, without touching the
 * controller, service, or registry.
 */
final class DateAggregator implements AggregatorInterface
{
    public function supports(string $type): bool
    {
        return $type === 'date';
    }

    /**
     * @param  array<int, string>  $answers
     * @return array<string, int> date => number of files with that date
     */
    public function aggregate(array $answers, ?array $options): array
    {
        $counts = [];

        foreach ($answers as $date) {
            $counts[$date] = ($counts[$date] ?? 0) + 1;
        }

        ksort($counts);

        return $counts;
    }
}
