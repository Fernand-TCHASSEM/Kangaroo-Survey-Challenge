<?php

namespace App\Aggregation;

use InvalidArgumentException;

final class AggregatorRegistry
{
    /**
     * @param  array<int, AggregatorInterface>  $aggregators
     */
    public function __construct(private readonly array $aggregators)
    {
    }

    public function get(string $type): AggregatorInterface
    {
        foreach ($this->aggregators as $aggregator) {
            if ($aggregator->supports($type)) {
                return $aggregator;
            }
        }

        throw new InvalidArgumentException("No aggregator registered for question type [{$type}].");
    }
}
