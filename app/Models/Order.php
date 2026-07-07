<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'store_id',
        'voucher_id',
        'total_amount',
        'discount_amount',
        'shipping_cost',
        'final_amount',
        'status', // pending, processing, shipped, completed, cancelled, returned
        'notes',
        'shipping_address',
        'shipping_recipient_name',
        'shipping_recipient_phone',
        'shipping_courier',
        'shipping_estimate',
        'tracking_number',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id');
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class, 'order_id');
    }
}
