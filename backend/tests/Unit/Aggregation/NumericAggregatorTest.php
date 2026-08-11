<?php

namespace Tests\Unit\Aggregation;

use App\Aggregation\NumericAggregator;
use PHPUnit\Framework\TestCase;

class NumericAggregatorTest extends TestCase
{
    public function test_supports_numeric_type_only(): void
    {
        $aggregator = new NumericAggregator();

        $this->assertTrue($aggregator->supports('numeric'));
        $this->assertFalse($aggregator->supports('qcm'));
    }

    public function test_averages_answers_across_files(): void
    {
        // Answers for XX1 (files 1, 2, 12, 13, 14).
        $answers = [670, 546, 1400, 400, 470];

        $result = (new NumericAggregator())->aggregate($answers, null);

        $this->assertSame(697.2, $result);
    }
}
