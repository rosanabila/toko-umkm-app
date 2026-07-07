<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'product_id',
        'user_id',
        'rating',
        'comment',
        'is_moderated',
        'moderated_by',
        'moderation_notes',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function moderatedBy()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
