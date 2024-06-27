<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/test', function (Request $request) {
//     dd('test');
// });
// Route::post('/test', [GoalController::class, 'store']);

Route::middleware('guest')->group(function () {
    Route::prefix('/login')->group(function () {
        Route::get('', [AuthController::class, 'login'])->name('login');
        Route::post('', [AuthController::class, 'handleLogin']);
        Route::get('/{provider}/redirect', [AuthController::class, 'providerRedirect']);
        Route::get('/{provider}/callback', [AuthController::class, 'providerCallback']);
    });
    Route::prefix('/register')->group(function () {
        Route::get('', [AuthController::class, 'register'])->name('register');
        Route::post('', [AuthController::class, 'handleRegister']);
    });
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'handleForgotPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password/{token}', [AuthController::class, 'handleResetPassword'])
        ->name('password.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [GoalController::class, 'index'])->name('dashboard');

    Route::delete('/logout', [AuthController::class, 'handleLogout']);

    Route::prefix('/users')->group(function () {
        Route::put('/{id}', [UserController::class, 'update']);

        Route::prefix('/self')->group(function () {
            Route::get('', [UserController::class, 'showSelf']);
            Route::get('/edit', [UserController::class, 'editSelf']);

            Route::prefix('/email')->group(function () {
                Route::withoutMiddleware('verified')->group(function () {
                    Route::get('/verify', function (Request $request) {
                        return $request->user()->hasVerifiedEmail()  == false ?
                            Inertia::render('Auths/VerifyEmail') :
                            redirect()->back()->with('message', 'User has already verified email.');
                    })->name('verification.notice');
                    Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
                        $request->fulfill();
                        return redirect('/')->with('message', 'Email verified successfully.');
                    })->middleware('signed')->name('verification.verify');
                    Route::post('/verification-notification', function (Request $request) {
                        $request->user()->sendEmailVerificationNotification();
                        return back()->with('message', 'Verification link sent!');
                    })->middleware('throttle:6,1')->name('verification.send');
                });
            });
        });
    });

    Route::prefix('/goals')->group(function () {
        Route::get('', [GoalController::class, 'index']);
        Route::post('', [GoalController::class, 'store']);
        Route::get('/{goal}', [GoalController::class, 'edit']);
        Route::get('/{goal}/edit', [GoalController::class, 'edit']);
        Route::put('/{goal}', [GoalController::class, 'update']);
        Route::delete('/{goal}', [GoalController::class, 'destroy']);
    });
});
