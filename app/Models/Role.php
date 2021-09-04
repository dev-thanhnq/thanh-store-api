<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = [
        'name',
        'is_protected',
        'description',
        'permission'
    ];

    public function permissions() 
    {
        return $this->belongsToMany(Permission::class);
    }
}
