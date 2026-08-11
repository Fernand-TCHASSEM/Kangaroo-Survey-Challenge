<?php

namespace App\Http\Controllers;

use App\Services\SurveyAggregationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct(private readonly SurveyAggregationService $surveys)
    {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->surveys->list($request->query('q')));
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
