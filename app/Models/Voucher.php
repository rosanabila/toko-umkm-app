<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'code',
        'type',
        'value',
        'min_spend',
        'start_date',
        'end_date',
        'active',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function isValidFor($amount): bool
    {
        $today = date('Y-m-d');
        return $this->active 
            && $this->start_date <= $today 
            && $this->end_date >= $today 
            && $amount >= $this->min_spend;
    }

    public function calculateDiscount($amount): float
    {
        if (!$this->isValidFor($amount)) {
            return 0.00;
        }

        if ($this->type === 'percent') {
            return $amount * ($this->value / 100);
        }

        return min($this->value, $amount);
    }
}
