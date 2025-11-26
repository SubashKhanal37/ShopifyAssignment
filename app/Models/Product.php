<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'shopify_id',
        'title',
        'handle',
        'description',
        'vendor',
        'product_type',
        'status',
        'tags',
        'images',
        'variants',
    ];

    protected $casts = [
        'images' => 'array',
        'variants' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_id');
    }
}
