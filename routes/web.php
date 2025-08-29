<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Webhook route untuk n8n (tidak perlu auth)
Route::post('/webhook/n8n/booking-notification', [WebhookController::class, 'bookingNotification'])
    ->name('webhook.booking.notification');

// API route untuk Telegram callback (tidak perlu auth tapi perlu token)
Route::post('/api/telegram/booking-action', [TelegramController::class, 'handleBookingAction'])
    ->name('api.telegram.booking-action');

Route::get('/dashboard', function () {
    // Redirect berdasarkan role user
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('guru.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes untuk Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Lihat jadwal lab keseluruhan
    Route::get('/lab-schedule', [AdminController::class, 'labSchedule'])->name('lab-schedule');
    
    // Kelola jadwal pakem
    Route::get('/schedules', [AdminController::class, 'schedules'])->name('schedules');
    Route::get('/schedules/create', [AdminController::class, 'createSchedule'])->name('schedules.create');
    Route::post('/schedules', [AdminController::class, 'storeSchedule'])->name('schedules.store');
    Route::get('/schedules/{schedule}/edit', [AdminController::class, 'editSchedule'])->name('schedules.edit');
    Route::put('/schedules/{schedule}', [AdminController::class, 'updateSchedule'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [AdminController::class, 'destroySchedule'])->name('schedules.destroy');
    
    // Kelola peminjaman
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{booking}/approve', [AdminController::class, 'approveBooking'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [AdminController::class, 'rejectBooking'])->name('bookings.reject');
    Route::delete('/bookings/{booking}/cancel', [AdminController::class, 'cancelBooking'])->name('bookings.cancel');
});

// Routes untuk Guru
Route::middleware(['auth'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/booking/create', [GuruController::class, 'createBooking'])->name('booking.create');
    Route::post('/booking', [GuruController::class, 'storeBooking'])->name('booking.store');
    Route::get('/my-bookings', [GuruController::class, 'myBookings'])->name('my-bookings');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
