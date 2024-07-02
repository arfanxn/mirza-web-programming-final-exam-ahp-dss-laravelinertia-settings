<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('guest')->group(function () {
    Route::prefix('/login')->group(function () {
        Route::post('', [AuthController::class, 'handleLogin']);
    });
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/', [GoalController::class, 'index'])->name('dashboard');

    Route::delete('/logout', [AuthController::class, 'handleLogout']);

    Route::prefix('/users')->group(function () {
        Route::put('/{id}', [UserController::class, 'update']);

        Route::prefix('/self')->group(function () {
            Route::get('', [UserController::class, 'showSelf']);
        });
    });

    Route::prefix('/goals')->group(function () {
        Route::get('', [GoalController::class, 'index']);
        Route::post('', [GoalController::class, 'store']);
        Route::get('/{goal}', [GoalController::class, 'show']);
        Route::get('/{goal}/rankingization', [GoalController::class, 'rankingization']);
        Route::put('/{goal}', [GoalController::class, 'update']);
        Route::delete('/{goal}', [GoalController::class, 'destroy']);
    });
});
