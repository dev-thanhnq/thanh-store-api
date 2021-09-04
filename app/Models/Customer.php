<?php
namespace App\Models;
use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    const PREFIX_CODE = 'KH';

    protected $table = 'customers';
    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
    ];

    public static function generateCode()
    {
        $count = DB::collection('customers')->count();
        return self::PREFIX_CODE . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
    
    public function order()
    {
        return $this->hasMany(Order::class);
    }

}
