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
     * @return array<int, array{code: string, name: string}> entries whose name
     *   or code contains $search (case-insensitive), or all of them if empty
     */
    public function list(?string $search = null): array
    {
        $codes = $this->surveys->codes();

        if ($search === null || $search === '') {
            return $codes;
        }

        $term = mb_strtolower($search);

        return array_values(array_filter(
            $codes,
            fn (array $entry) => str_contains(mb_strtolower($entry['name']), $term)
                || str_contains(mb_strtolower($entry['code']), $term),
        ));
    }
}
