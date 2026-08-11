<?php

namespace App\Http\Controllers;

use App\Services\SurveyAggregationService;
use Illuminate\Http\JsonResponse;

class SurveyController extends Controller
{
    public function __construct(private readonly SurveyAggregationService $surveys)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->surveys->list());
    }

    public function show(string $code): JsonResponse
    {
        $result = $this->surveys->aggregateFor($code);

        if ($result === null) {
            abort(404);
        }

        return response()->json($result);
    }
}
