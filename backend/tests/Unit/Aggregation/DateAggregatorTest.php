<?php

namespace Tests\Unit\Aggregation;

use App\Aggregation\DateAggregator;
use PHPUnit\Framework\TestCase;

class DateAggregatorTest extends TestCase
{
    public function test_supports_date_type_only(): void
    {
        $aggregator = new DateAggregator();

        $this->assertTrue($aggregator->supports('date'));
        $this->assertFalse($aggregator->supports('qcm'));
    }

    public function test_counts_occurrences_per_date_sorted_ascending(): void
    {
        $answers = ['2026-01-05', '2026-01-01', '2026-01-05'];

        $result = (new DateAggregator())->aggregate($answers, null);

        $this->assertSame([
            '2026-01-01' => 1,
            '2026-01-05' => 2,
        ], $result);
    }
}
