<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Osiset\ShopifyApp\Contracts\ShopModel as IShopModel;
use Osiset\ShopifyApp\Traits\ShopModel;

class User extends Authenticatable implements IShopModel
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, ShopModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'products_last_synced_at' => 'datetime',
            'collections_last_synced_at' => 'datetime',
            'orders_last_synced_at' => 'datetime',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'shop_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shop_id');
    }
}
