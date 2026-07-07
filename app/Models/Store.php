<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'address',
        'phone',
        'operating_hours_open',
        'operating_hours_close',
        'shipping_areas',
    ];

    protected $casts = [
        'shipping_areas' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'store_id');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'store_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'store_id');
    }
}
