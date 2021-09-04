<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $fillable = [
        'name',
        'display_name',
        'group_name',
        'description'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
