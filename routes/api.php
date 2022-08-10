<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);

    Route::prefix('formation')->group(function () {
        Route::get('', [\App\Http\Controllers\Api\FormationController::class, 'index']);
        Route::post('', [\App\Http\Controllers\Api\FormationController::class, 'store']);
        Route::post('search/{word}', [\App\Http\Controllers\Api\FormationController::class, 'show']);

        Route::post('{formation_id}', [\App\Http\Controllers\Api\AreaController::class, 'index']);
        Route::prefix('{formation_id}/tasks')->group(function () {
            Route::get('', [\App\Http\Controllers\Api\TaskController::class, 'index']);
            Route::post('', [\App\Http\Controllers\Api\TaskController::class, 'store']);
        } );

    });
});
