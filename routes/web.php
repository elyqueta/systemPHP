<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\InstitutionController as AdminInstitutionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [AdminInstitutionController::class, 'overview'])->name('dashboard');

    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');

    Route::resource('institutions', AdminInstitutionController::class)->only(['index', 'create', 'store', 'show']);

    Route::post('/institutions/{institution}/activate', [AdminInstitutionController::class, 'activate'])->name('institutions.activate');
    Route::post('/institutions/{institution}/deactivate', [AdminInstitutionController::class, 'deactivate'])->name('institutions.deactivate');
});
