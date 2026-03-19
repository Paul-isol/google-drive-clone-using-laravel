<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('myfiles/{folder?}', [FileController::class, 'index'])
        ->where('folder', '(.*)')
        ->name('myfiles');
    Route::inertia('shared-with-me', 'SharedWithMe')->name('shared-with-me');
    Route::inertia('shared-by-me', 'SharedByMe')->name('shared-by-me');
    Route::inertia('trash', 'Trash')->name('trash');

    Route::post('folder/create', [FileController::class, 'createFolder'])->name('folder.create');
    Route::post('file/upload', [FileController::class, 'upload'])->name('file/upload');
    Route::delete('file/destroy', [FileController::class, 'destroy'])->name('file/destroy');
});

require __DIR__.'/settings.php';
