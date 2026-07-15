<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\Api\ImageController as ApiImageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CompanyController,
    HomeController,
    DashboardController,
    UserController,
    PostController,
    ProductController,
    PlanController,
    CategoryController,
};
use App\Http\Controllers\Company as CompanyModule;

Auth::routes();

Route::middleware(['permission'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware(['permission','auth'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('companies', CompanyController::class);
    Route::resource('users', UserController::class);
    Route::resource('posts', PostController::class);
    Route::resource('products', ProductController::class);
    Route::resource('plans', PlanController::class);
    Route::resource('categories', CategoryController::class);
    Route::post('upload-image', [ApiImageController::class, 'upload'])->name('upload-image');
});

Route::middleware(['permission','auth'])->name('company.')->prefix('company')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'company'])->name('dashboard');
    Route::get('/mycompany', [CompanyController::class, 'mycompany'])->name('mycompany');
    Route::put('/mycompany/update', [CompanyController::class, 'mycompanyUpdate'])->name('mycompany.update');

    Route::resource('products', CompanyModule\ProductController::class);
    Route::resource('posts', CompanyModule\PostController::class);
    Route::resource('clients', CompanyModule\ClientController::class);
    Route::resource('galleries', CompanyModule\GalleryController::class);
    Route::post('galleries/{gallery}/upload', [CompanyModule\GalleryController::class, 'uploadImage'])->name('galleries.upload');
    Route::delete('images/{image}', [CompanyModule\GalleryController::class, 'destroyImage'])->name('galleries.destroyImage');
    Route::resource('files', CompanyModule\FileController::class);
    Route::resource('categories', CompanyModule\CategoryController::class);

    Route::post('/users/create', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}/destroy', [UserController::class, 'destroy'])->name('users.destroy');
});
