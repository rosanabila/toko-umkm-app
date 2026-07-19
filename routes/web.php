<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\SellerVoucherController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AdminCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/store/{slug}', [ProductController::class, 'storeShow'])->name('store.show');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    
    // Buyer Roles & Actions
    Route::middleware(['role:pembeli'])->group(function () {
        // Carts
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        
        // Checkout & Orders
        Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
        Route::post('/checkout/process', [CartController::class, 'processCheckout'])->name('cart.processCheckout');
        Route::get('/buyer/orders', [BuyerController::class, 'orders'])->name('buyer.orders');
        Route::get('/buyer/order/{id}', [BuyerController::class, 'orderDetail'])->name('buyer.orderDetail');
        Route::post('/buyer/order/{id}/pay', [BuyerController::class, 'submitPayment'])->name('buyer.submitPayment');
        Route::post('/buyer/order/{id}/cancel', [BuyerController::class, 'cancelOrder'])->name('buyer.cancelOrder');
        Route::post('/buyer/order/{id}/return', [BuyerController::class, 'submitReturn'])->name('buyer.submitReturn');
        Route::post('/buyer/review/add', [BuyerController::class, 'submitReview'])->name('buyer.submitReview');
        
        // Wishlists CRUD
        Route::resource('/wishlist', WishlistController::class)->only(['index', 'store', 'destroy']);
    });

    // Invoice PDF - Accessible by buyer (owner) and seller (store owner) or admin
    Route::get('/orders/{id}/invoice', [ReportController::class, 'invoicePdf'])->name('orders.invoicePdf');

    // Seller Roles & Actions
    Route::middleware(['role:penjual'])->prefix('seller')->name('seller.')->group(function () {
        Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [SellerController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [SellerController::class, 'profileUpdate'])->name('profileUpdate');
        
        // Products CRUD + custom variant endpoints
        Route::resource('/products', SellerProductController::class);
        
        // Orders Management
        Route::get('/orders', [SellerController::class, 'orders'])->name('orders');
        Route::get('/order/{id}', [SellerController::class, 'orderDetail'])->name('orderDetail');
        Route::post('/order/{id}/update-status', [SellerController::class, 'updateOrderStatus'])->name('updateOrderStatus');
        Route::post('/order/{id}/confirm-payment', [SellerController::class, 'confirmPayment'])->name('confirmPayment');
        
        // Vouchers CRUD
        Route::resource('/vouchers', SellerVoucherController::class);
        
        // Reports
        Route::get('/reports', [SellerController::class, 'reports'])->name('reports');
        Route::get('/reports/stock/pdf', [ReportController::class, 'stockPdf'])->name('reports.stockPdf');
        Route::get('/reports/sales/csv', [ReportController::class, 'salesCsv'])->name('reports.salesCsv');
        Route::get('/reports/orders/csv', [ReportController::class, 'ordersCsv'])->name('reports.ordersCsv');
        Route::get('/order/{id}/delivery-note', [ReportController::class, 'deliveryNotePdf'])->name('deliveryNotePdf');
    });

    // Admin Roles & Actions
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])->name('updateUserRole');
        Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/{id}/moderate', [AdminController::class, 'moderateReview'])->name('moderateReview');
        Route::get('/returns', [AdminController::class, 'returns'])->name('returns');
        Route::post('/returns/{id}/moderate', [AdminController::class, 'moderateReturn'])->name('moderateReturn');
        Route::resource('/categories', AdminCategoryController::class);
    });
});
