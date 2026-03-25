<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToppageController;
use App\Http\Controllers\ReservationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
        //試し
Route::get('/mail', [ReservationController::class, 'mail']);
Route::get('/',[ToppageController::class, 'toppage'])->name('top');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->prefix('auth')->group(function () {

    Route::get('/setting', [ReservationController::class, 'create'])->name('creste');

    Route::post('/setting', [ReservationController::class, 'setting'])->name('setting');

    Route::post('/setting/check', [ReservationController::class, 'check'])->name('check');

    Route::post('/setting/{User}/store', [ReservationController::class, 'store'])->name('store');

    Route::get('/{user}/list', [ReservationController::class, 'list'])->name('reservation.list');

    });
