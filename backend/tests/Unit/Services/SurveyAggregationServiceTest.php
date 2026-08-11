<?php

namespace Tests\Unit\Services;

use App\Aggregation\AggregatorRegistry;
use App\Aggregation\NumericAggregator;
use App\Aggregation\QcmAggregator;
use App\Repositories\SurveyRepository;
use App\Services\SurveyAggregationService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SurveyAggregationServiceTest extends TestCase
{
    private function service(): SurveyAggregationService
    {
        return new SurveyAggregationService(
            new SurveyRepository('surveys'),
            new AggregatorRegistry([new QcmAggregator(), new NumericAggregator()]),
        );
    }

    private function survey(array $qcmAnswer, int $numericAnswer): array
    {
        return [
            'survey' => ['name' => 'Paris', 'code' => 'XX1'],
            'questions' => [
                [
                    'type' => 'qcm',
                    'label' => 'Products?',
                    'options' => ['A', 'B'],
                    'answer' => $qcmAnswer,
                ],
                [
                    'type' => 'numeric',
                    'label' => 'Count?',
                    'options' => null,
                    'answer' => $numericAnswer,
                ],
            ],
        ];
    }

    public function test_returns_null_for_unknown_code(): void
    {
        Storage::fake('surveys');

        $this->assertNull($this->service()->aggregateFor('UNKNOWN'));
    }

    public function test_aggregates_each_question_across_files_of_the_code(): void
    {
        Storage::fake('surveys');
        Storage::disk('surveys')->put('1.json', json_encode($this->survey([true, false], 10)));
        Storage::disk('surveys')->put('2.json', json_encode($this->survey([false, true], 20)));

        $result = $this->service()->aggregateFor('XX1');

        $this->assertSame([
            ['type' => 'qcm', 'label' => 'Products?', 'result' => ['A' => 1, 'B' => 1]],
            ['type' => 'numeric', 'label' => 'Count?', 'result' => 15.0],
        ], $result);
    }

    public function test_list_delegates_to_repository_codes(): void
    {
        Storage::fake('surveys');
        Storage::disk('surveys')->put('1.json', json_encode($this->survey([true, false], 10)));

        $this->assertSame([['code' => 'XX1', 'name' => 'Paris']], $this->service()->list());
    }
}
