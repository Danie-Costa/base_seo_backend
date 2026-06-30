<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BillController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CompanyController,
    HomeController,
    DashboardController,
    UserController,
};

// require __DIR__.'/auth.php';
Auth::routes();
Route::middleware(['permission'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware(['permission','auth'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('companies', CompanyController::class);
    Route::resource('users', UserController::class);
});

