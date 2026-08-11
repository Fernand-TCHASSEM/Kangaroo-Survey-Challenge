<?php

namespace App\Services;

use App\Aggregation\AggregatorRegistry;
use App\Repositories\SurveyRepository;

final class SurveyAggregationService
{
    public function __construct(
        private readonly SurveyRepository $surveys,
        private readonly AggregatorRegistry $aggregators,
    ) {
    }

    /**
     * @return array<int, array{type: string, label: string, result: mixed}>|null null when the code is unknown
     */
    public function aggregateFor(string $code): ?array
    {
        $surveys = $this->surveys->forCode($code);

        if ($surveys === null) {
            return null;
        }

        $results = [];

        foreach ($surveys[0]['questions'] as $index => $question) {
            $answers = array_map(
                fn (array $survey) => $survey['questions'][$index]['answer'],
                $surveys,
            );

            $results[] = [
                'type' => $question['type'],
                'label' => $question['label'],
                'result' => $this->aggregators->get($question['type'])->aggregate($answers, $question['options']),
            ];
        }

        return $results;
    }

    /**
     * @return array<int, array{code: string, name: string}>
     */
    public function list(): array
    {
        return $this->surveys->codes();
    }
}
