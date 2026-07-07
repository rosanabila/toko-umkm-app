<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'reason',
        'evidence_image',
        'status', // pending, approved, rejected
        'admin_notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
