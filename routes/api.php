<?php

use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\AIRoadmapController;
use App\Http\Controllers\AIAssistantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// AI Routes - menggunakan web middleware untuk session auth
Route::middleware('web')->group(function () {
    Route::post('/ai/review', [AiController::class, 'review']);
    Route::post('/ai/roadmap', [AIRoadmapController::class, 'generateRoadmap']);
    Route::post('/ai-assistant', [AIAssistantController::class, 'askQuestion']);
});
