<?php

namespace Tests\Unit\Repositories;

use App\Repositories\SurveyRepository;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SurveyRepositoryTest extends TestCase
{
    private function repository(): SurveyRepository
    {
        return new SurveyRepository('surveys');
    }

    private function putSurvey(string $file, string $name, string $code): void
    {
        Storage::disk('surveys')->put($file, json_encode([
            'survey' => ['name' => $name, 'code' => $code],
            'questions' => [],
        ]));
    }

    public function test_groups_surveys_by_code(): void
    {
        Storage::fake('surveys');
        $this->putSurvey('1.json', 'Paris', 'XX1');
        $this->putSurvey('2.json', 'Paris', 'XX1');
        $this->putSurvey('3.json', 'Melun', 'XX3');

        $grouped = $this->repository()->groupedByCode();

        $this->assertCount(2, $grouped['XX1']);
        $this->assertCount(1, $grouped['XX3']);
    }

    public function test_codes_returns_one_entry_per_code_with_name(): void
    {
        Storage::fake('surveys');
        $this->putSurvey('1.json', 'Paris', 'XX1');
        $this->putSurvey('2.json', 'Melun', 'XX3');

        $codes = $this->repository()->codes();

        $this->assertSame([
            ['code' => 'XX1', 'name' => 'Paris'],
            ['code' => 'XX3', 'name' => 'Melun'],
        ], $codes);
    }

    public function test_for_code_returns_null_for_unknown_code(): void
    {
        Storage::fake('surveys');

        $this->assertNull($this->repository()->forCode('UNKNOWN'));
    }

    public function test_ignores_non_json_files(): void
    {
        Storage::fake('surveys');
        $this->putSurvey('1.json', 'Paris', 'XX1');
        Storage::disk('surveys')->put('.gitkeep', '');

        $this->assertCount(1, $this->repository()->all());
    }
}
