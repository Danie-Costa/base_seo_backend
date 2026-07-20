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
    LeadController,
    PaymentController,
    WebhookController,
};
use App\Http\Controllers\Company as CompanyModule;

Auth::routes();

Route::post('/lead', [LeadController::class, 'store'])->name('lead.store');

Route::post('/webhook/mercadopago', [WebhookController::class, 'mercadopago'])->name('webhook.mercadopago');

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
    Route::resource('leads', LeadController::class)->only(['index', 'destroy']);
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
    Route::get('plans', [CompanyModule\PlanController::class, 'index'])->name('plans.index');
    Route::get('plans/{plan}/checkout', [CompanyModule\PlanController::class, 'checkout'])->name('plans.checkout');
    Route::get('plans/{plan}/success', [CompanyModule\PlanController::class, 'success'])->name('plans.success');
    Route::get('plans/{plan}/failure', [CompanyModule\PlanController::class, 'failure'])->name('plans.failure');
    Route::post('plans/cancel', [CompanyModule\PlanController::class, 'cancel'])->name('plans.cancel');
    Route::get('orders', [CompanyModule\OrderController::class, 'index'])->name('orders.index');

    Route::post('/users/create', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}/destroy', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/{payment}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/{payment}/pix', [PaymentController::class, 'processPix'])->name('pix');
    Route::post('/{payment}/card', [PaymentController::class, 'processCard'])->name('card');
    Route::post('/{payment}/boleto', [PaymentController::class, 'processBoleto'])->name('boleto');
    Route::get('/{payment}/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/{payment}/failure', [PaymentController::class, 'failure'])->name('failure');
});
