<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->timestamps();

            // Composite index for reporting
            $table->index(['product_id', 'quantity'], 'idx_order_items_product_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
