<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class OrderHistory extends Model
{
    protected $table = 'order_histories';
    protected $fillable = [
        'order_id',
        'status',
        'user_id'
    ];

    const STATUS = [
        'PENDING' => 1,
        'APPROVED' => 2,
        'DELIVERED' => 3,
        'SHIPPED' => 4,
        'CANCELED' => 5,
        'RETURNED' => 6
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */

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
