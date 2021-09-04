<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Author extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'description',
    ];
}
