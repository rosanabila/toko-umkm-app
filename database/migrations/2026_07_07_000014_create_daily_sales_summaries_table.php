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
        Schema::create('daily_sales_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->date('date');
            $table->decimal('total_sales', 15, 2)->default(0.00);
            $table->integer('order_count')->default(0);
            $table->timestamps();

            // Composite unique index
            $table->unique(['store_id', 'date'], 'idx_store_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales_summaries');
    }
};
