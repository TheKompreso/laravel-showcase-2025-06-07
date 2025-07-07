<?php

use App\Http\Controllers\API\v1\BookingController;
use App\Http\Middleware\CheckApiToken;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => [
        CheckApiToken::class
    ],
], function () {
    Route::prefix('v1')->name('api.v1.')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::patch('/bookings/{booking}/slots/{slot}', [BookingController::class, 'updateSlot'])->name('bookings.slots.update');
        Route::post('/bookings/{booking}/slots', [BookingController::class, 'addSlot'])->name('bookings.slots.add');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    });
});
