<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'status', // pending, confirmed, failed, refunded
        'payment_receipt',
        'confirmed_at',
        'confirmed_by',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
