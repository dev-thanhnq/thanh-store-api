<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'product_id',
        'sku',
        'name',
        'image',
        'description',
        'weight',
        'sale_price',
        'original_price',
        'quantity_in_stock',
        'quantity',
        'total_value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'weight' => 'integer',
        'sale_price' => 'integer',
        'original_price' => 'integer',
        'quantity_in_stock' => 'integer',
        'quantity' => 'integer',
        'total_value' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
   

    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
}
