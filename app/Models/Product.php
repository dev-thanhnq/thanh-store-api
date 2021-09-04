<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'sku',
        'name',
        'image',
        'category_ids',
        'author_id',
        'description',
        'weight',
        'sale_price',
        'original_price',
        'quantity_in_stock',
    ];

    protected $casts = [
        'weight' => 'integer',
        'sale_price' => 'integer',
        'original_price' => 'integer',
        'quantity_in_stock' => 'integer',
    ];

    const PREFIX_CODE = 'SP';

    public function categories()
    {
        return $this->belongsToMany(
            Category::class, null, 'product_ids', 'category_ids'
        );
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function histories()
    {
        return $this->hasMany(ProductHistory::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function generateCode()
    {
        $product = Product::orderBy('created_at','DESC')->first();
        if(empty($product)) {
            $sku = 0;
        } else {
            $sku = (int) substr($product->sku, 2) +1;
        }
        return self::PREFIX_CODE . str_pad($sku, 4, '0', STR_PAD_LEFT);
    }
}
