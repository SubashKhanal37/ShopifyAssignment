<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'shopify_id',
        'name',
        'financial_status',
        'fulfillment_status',
        'total_price',
        'currency',
        'processed_at',
        'customer_name',
        'customer_email',
        'raw_payload',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_id');
    }
}
