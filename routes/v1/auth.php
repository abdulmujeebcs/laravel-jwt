<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::controller(AuthController::class)->group(function ($group) {
    Route::post('/login', 'login')
        ->name('auth.login');

    Route::post('/register', 'register')
        ->name('auth.register');

    Route::post('/logout', 'logout')
        ->name('auth.logout');

    Route::get('/user', 'getUser')
        ->name('auth.user');

    Route::put('/update-profile', 'updateProfile')
        ->name('auth.update-profile');

    Route::post('/refresh/token', 'refresh')
        ->name('auth.refresh-token');
});
