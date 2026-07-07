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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('store_id')->constrained('stores')->onDelete('restrict');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('final_amount', 12, 2);
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled', 'returned'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('shipping_address');
            $table->string('shipping_recipient_name');
            $table->string('shipping_recipient_phone');
            $table->string('shipping_courier');
            $table->string('shipping_estimate');
            $table->string('tracking_number')->nullable();
            $table->timestamps();

            // Composite index for reporting
            $table->index(['store_id', 'status', 'created_at'], 'idx_orders_store_status_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
