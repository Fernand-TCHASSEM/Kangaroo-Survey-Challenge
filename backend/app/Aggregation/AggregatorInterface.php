<?php

namespace App\Aggregation;

interface AggregatorInterface
{
    public function supports(string $type): bool;

    /**
     * @param  array<int, mixed>  $answers  one answer per survey file of the same code
     * @param  array<int, string>|null  $options  the question's options, if any
     */
    public function aggregate(array $answers, ?array $options): mixed;
}
