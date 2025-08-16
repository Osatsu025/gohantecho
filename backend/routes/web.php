<?php

use App\Http\Controllers\DiaryController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/favorite', [MenuController::class, 'favorite'])->name('menus.favorite');

    Route::resource('menus.memos', MemoController::class)->only('store', 'update', 'destroy')->scoped();
    
    Route::resource('diaries', DiaryController::class);
});

require __DIR__.'/auth.php';
