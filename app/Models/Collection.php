<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'shopify_id',
        'title',
        'handle',
        'description',
        'products_count',
        'sort_order',
    ];

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_id');
    }
}
