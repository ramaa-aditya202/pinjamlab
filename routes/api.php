<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Telegram callback route (tidak perlu CSRF karena sudah di routes/api.php)
Route::post('/telegram/booking-action', [TelegramController::class, 'handleBookingAction'])
    ->name('api.telegram.booking-action');
