<?php

use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

// list.json must be registered before {code}.json, otherwise "list" would
// itself match the {code} constraint and never reach this route.
Route::get('/list.json', [SurveyController::class, 'index']);
Route::get('/{code}.json', [SurveyController::class, 'show'])->where('code', '[A-Za-z0-9]+');
