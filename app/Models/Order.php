<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{
    const STATUS = [
        'PENDING' => 1,
        'APPROVED' => 2,
        'DELIVERED' => 3,
        'SHIPPED' => 4,
        'CANCELED' => 5,
        'RETURNED' => 6
    ];

    protected $table = 'orders';
    protected $fillable = [
        'customer_id',
        'code',
        'total_value',
        'status',
        'note',
        'delivery_address',
        'receiver',
        'receiver_phone',
        'transport_fee',
        'total_original_price',
        'creator_id'
    ];

    const PREFIX_CODE = 'HD';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_value' => 'integer',
        'transport_fee' => 'integer',
        'total_original_price' => 'integer'
    ];

    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public static function generateCode()
    {
        $order = Order::orderBy('created_at','DESC')->first();
        if(empty($order)) {
            $code = 0;
        } else {
            $code = (int) substr($order->code, 2) +1;
        }
        return self::PREFIX_CODE . str_pad($code, 4, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
