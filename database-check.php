<?php
/**
 * Database Seeding Verification Script
 * Outputs count of records in all application tables.
 */
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = [
    'users' => \App\Models\User::class,
    'stores' => \App\Models\Store::class,
    'categories' => \App\Models\Category::class,
    'products' => \App\Models\Product::class,
    'product_variants' => \App\Models\ProductVariant::class,
    'vouchers' => \App\Models\Voucher::class,
    'orders' => \App\Models\Order::class,
    'order_items' => \App\Models\OrderItem::class,
    'payments' => \App\Models\Payment::class,
    'reviews' => \App\Models\Review::class,
    'order_histories' => \App\Models\OrderHistory::class,
    'order_returns' => \App\Models\OrderReturn::class,
    'carts' => \App\Models\Cart::class,
];

echo "====================================\n";
echo " TOKOKITA DATABASE RECORD COUNT      \n";
echo "====================================\n";

foreach ($tables as $name => $model) {
    try {
        $count = $model::count();
        printf("%-24s : %d records\n", $name, $count);
    } catch (\Exception $e) {
        printf("%-24s : ERROR (%s)\n", $name, $e->getMessage());
    }
}

// Check category_product pivot table count
try {
    $pivotCount = \DB::table('category_product')->count();
    printf("%-24s : %d records\n", 'category_product (pivot)', $pivotCount);
} catch (\Exception $e) {
    printf("%-24s : ERROR (%s)\n", 'category_product (pivot)', $e->getMessage());
}

// Check daily_sales_summaries summary table count
try {
    $summaryCount = \DB::table('daily_sales_summaries')->count();
    printf("%-24s : %d records\n", 'daily_sales_summaries', $summaryCount);
} catch (\Exception $e) {
    printf("%-24s : ERROR (%s)\n", 'daily_sales_summaries', $e->getMessage());
}

// Check wishlists table count
try {
    $wishlistsCount = \DB::table('wishlists')->count();
    printf("%-24s : %d records\n", 'wishlists', $wishlistsCount);
} catch (\Exception $e) {
    printf("%-24s : ERROR (%s)\n", 'wishlists', $e->getMessage());
}

echo "====================================\n";
