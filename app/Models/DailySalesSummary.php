<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySalesSummary extends Model
{
    use HasFactory;

    protected $table = 'daily_sales_summaries';

    protected $fillable = [
        'store_id',
        'date',
        'total_sales',
        'order_count',
    ];

    protected $casts = [
        'date' => 'date',
        'total_sales' => 'decimal:2',
        'order_count' => 'integer',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
