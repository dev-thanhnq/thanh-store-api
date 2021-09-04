<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'description',
    ];

    public function products()
    {
        return $this->belongsToMany(
            Product::class, null, 'category_ids', 'products_ids'
        );
    }
}
