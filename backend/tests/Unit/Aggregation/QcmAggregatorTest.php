<?php

namespace Tests\Unit\Aggregation;

use App\Aggregation\QcmAggregator;
use PHPUnit\Framework\TestCase;

class QcmAggregatorTest extends TestCase
{
    public function test_supports_qcm_type_only(): void
    {
        $aggregator = new QcmAggregator();

        $this->assertTrue($aggregator->supports('qcm'));
        $this->assertFalse($aggregator->supports('numeric'));
    }

    public function test_counts_true_answers_per_option_across_files(): void
    {
        $options = ['Product 1', 'Product 2', 'Product 3', 'Product 4', 'Product 5', 'Product 6'];

        // Answers for XX1 (files 1, 2, 12, 13, 14).
        $answers = [
            [false, true, true, false, true, false],
            [false, false, false, false, true, false],
            [false, false, false, false, true, false],
            [false, true, false, false, true, false],
            [false, false, false, false, false, false],
        ];

        $result = (new QcmAggregator())->aggregate($answers, $options);

        $this->assertSame([
            'Product 1' => 0,
            'Product 2' => 2,
            'Product 3' => 1,
            'Product 4' => 0,
            'Product 5' => 4,
            'Product 6' => 0,
        ], $result);
    }

    public function test_returns_zero_counts_when_no_files(): void
    {
        $options = ['Product 1', 'Product 2'];

        $result = (new QcmAggregator())->aggregate([], $options);

        $this->assertSame(['Product 1' => 0, 'Product 2' => 0], $result);
    }
}
