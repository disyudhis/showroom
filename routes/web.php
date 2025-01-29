<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard.index');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    Volt::route('/detail/{car}', 'dashboard.detail-car')->name('dashboard.show');
    Route::view('input-data', 'input-data')->name('cars.store');
    Volt::route('/edit/{car}', 'input-data.edit')->name('cars.edit');
});

require __DIR__ . '/auth.php';
