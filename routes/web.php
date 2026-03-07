<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('myfiles', 'Myfiles')->name('myfiles');
    Route::inertia('shared-with-me', 'SharedWithMe')->name('shared-with-me');
    Route::inertia('shared-by-me', 'SharedByMe')->name('shared-by-me');
    Route::inertia('trash', 'Trash')->name('trash');
});

require __DIR__.'/settings.php';
