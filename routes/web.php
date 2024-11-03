<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecretController;
use App\Http\Controllers\ProfileController;

// Route::get('/', function () {
//     return view('home');
// })->name('secrets.create');

Route::get('/', [SecretController::class, 'create'])->name('secrets.create');
Route::get('/new/{secret}', [SecretController::class, 'success'])->name('secrets.success');
Route::get('/secret/{url_identifier}', [SecretController::class, 'show'])->name('secrets.show');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
