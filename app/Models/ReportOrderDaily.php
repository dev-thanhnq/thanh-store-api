<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ReportOrderDaily extends Model
{
    use HasFactory;
    protected $table = 'report_order_daily';
    protected $fillable = [
        'total_value',
        'date',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['label'];

    /**
     * Determine if the user is an administrator.
     *
     * @return string
     */
    public function getLabelAttribute()
    {
        return Carbon::parse($this->date)->format('d/m/Y');
    }
}
