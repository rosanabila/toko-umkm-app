<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'stock',
        'discount_percent',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    // Dynamic Attribute: Discounted Price
    public function getDiscountedPriceAttribute()
    {
        if ($this->discount_percent > 0) {
            return $this->price * (1 - ($this->discount_percent / 100));
        }
        return $this->price;
    }

    // Get average rating
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_moderated', false)->avg('rating') ?: 0;
    }
}
