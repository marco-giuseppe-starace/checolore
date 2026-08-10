<?php

use App\Http\Controllers\ChildController;
use App\Http\Controllers\PackConfirmationController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimetableEntryController;
use App\Http\Controllers\TodayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/today', [TodayController::class, 'index']);
    Route::post('children/{child}/pack/{subject}', [PackConfirmationController::class, 'toggle']);

    Route::apiResource('children', ChildController::class);

    Route::get('children/{child}/subjects', [SubjectController::class, 'index']);
    Route::post('children/{child}/subjects', [SubjectController::class, 'store']);
    Route::put('subjects/{subject}', [SubjectController::class, 'update']);
    Route::delete('subjects/{subject}', [SubjectController::class, 'destroy']);

    Route::get('children/{child}/timetable', [TimetableEntryController::class, 'index']);
    Route::post('children/{child}/timetable', [TimetableEntryController::class, 'store']);
    Route::delete('timetable/{timetableEntry}', [TimetableEntryController::class, 'destroy']);
});
