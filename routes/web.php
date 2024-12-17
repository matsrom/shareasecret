<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecretController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApiProxyController;


// Route::get('/', function () {
//     return view('home');
// })->name('secrets.create');

Route::get('/', [SecretController::class, 'create'])->name('secrets.create');
Route::get('/new/{secret}', [SecretController::class, 'success'])->name('secrets.success');
Route::get('/secret/{url_identifier}', [SecretController::class, 'show'])->name('secrets.show');
Route::get('/proxy/ip-location', [ApiProxyController::class, 'getIpLocation'])->name('api.getIpLocation');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('/secret-log/{secret}', [SecretController::class, 'log'])->name('secret.log');
    
});

Route::fallback(function() {
    return redirect()->route('dashboard')->with('status', [
        'message' => 'Error 404: site not found',
        'class' => 'toast-danger'
    ]);
});

require __DIR__.'/auth.php';
