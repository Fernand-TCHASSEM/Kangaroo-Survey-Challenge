<?php

namespace Tests\Unit\Aggregation;

use App\Aggregation\AggregatorRegistry;
use App\Aggregation\NumericAggregator;
use App\Aggregation\QcmAggregator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AggregatorRegistryTest extends TestCase
{
    public function test_returns_the_aggregator_that_supports_the_type(): void
    {
        $qcm = new QcmAggregator();
        $numeric = new NumericAggregator();
        $registry = new AggregatorRegistry([$qcm, $numeric]);

        $this->assertSame($qcm, $registry->get('qcm'));
        $this->assertSame($numeric, $registry->get('numeric'));
    }

    public function test_throws_on_unknown_type(): void
    {
        $registry = new AggregatorRegistry([new QcmAggregator()]);

        $this->expectException(InvalidArgumentException::class);

        $registry->get('unknown');
    }
}
