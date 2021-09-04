<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Cart extends Model
{
    const QUANTITY = [
        'DEFAULT' => 1
    ];
    use HasFactory;
    protected $table = 'carts';
    protected $fillable = [
        'product_id',
        'quantity',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
