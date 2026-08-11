<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

final class SurveyRepository
{
    public function __construct(private readonly string $disk)
    {
    }

    /**
     * @return array<int, array{survey: array{name: string, code: string}, questions: array<int, array>}>
     */
    public function all(): array
    {
        $surveys = [];

        foreach (Storage::disk($this->disk)->files() as $file) {
            if (! str_ends_with($file, '.json')) {
                continue;
            }

            $surveys[] = json_decode(Storage::disk($this->disk)->get($file), true);
        }

        return $surveys;
    }

    /**
     * @return array<string, array<int, array>> code => surveys sharing that code
     */
    public function groupedByCode(): array
    {
        $grouped = [];

        foreach ($this->all() as $survey) {
            $grouped[$survey['survey']['code']][] = $survey;
        }

        ksort($grouped);

        return $grouped;
    }

    /**
     * @return array<int, array{code: string, name: string}>
     */
    public function codes(): array
    {
        $codes = [];

        foreach ($this->groupedByCode() as $code => $surveys) {
            $codes[] = ['code' => $code, 'name' => $surveys[0]['survey']['name']];
        }

        return $codes;
    }

    /**
     * @return array<int, array>|null surveys for the given code, or null if unknown
     */
    public function forCode(string $code): ?array
    {
        return $this->groupedByCode()[$code] ?? null;
    }
}
