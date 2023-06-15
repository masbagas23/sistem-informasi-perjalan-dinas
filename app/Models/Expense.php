<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_VALIDATION = 1;
    const STATUS_DONE = 2;

    protected $fillable = [
        'code',
        'application_id',
        'total_nominal',
        'down_payment',
        'remaining_cost',
        'is_reimburse',
        'status',
        'status_reimburse',
        'reimburse_path',
    ];

    public static function generateCode($date)
    {
        $month      = substr($date, 5, 2);
        $year       = substr($date, 0, 4);
        $lastRow    = self::withTrashed()
        ->whereMonth('created_at',$month)
        ->whereYear('created_at',$year)
        ->count() + 1;
        return self::checkGenerateCode($lastRow, $year, $month);
    }

    public static function checkGenerateCode($row, $year, $month)
    {

        $code  = "BPP".substr($year,2,2).$month.trim(str_pad($row, 3, 0, STR_PAD_LEFT));
        if (self::withTrashed()->where('code', $code)->exists()) {
            $row++;
            return self::checkGenerateCode($row, $year, $month);
        }
        return $code;
    }

    public function application()
    {
        return $this->belongsTo(BusinessTripApplication::class, 'application_id', 'id')->withDefault();
    }

    public function details()
    {
        return $this->hasMany(ExpenseDetail::class,'expense_id','id');
    }
}
