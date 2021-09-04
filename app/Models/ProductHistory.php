<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ProductHistory extends Model
{
    protected $table = 'product_histories';
    protected $fillable = [
        'product_id',
        'creator_id',
        'quantity',
        'quantity_in_stock',
    ];

    public function user() {
        return $this->belongsTo(User::class,'creator_id');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
