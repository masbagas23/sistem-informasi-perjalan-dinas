<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DownPaymentRequest extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_WAITING = 1;
    const STATUS_APPROVE = 2;
    const STATUS_CANCEL = 3;
    const STATUS_REJECT = 4;

    protected $fillable = [
        'code',
        'application_id',
        'nominal',
        'status',
        'requested_by',
        'approved_by',
        'note',
        'file_path'
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
        $code  = "UMK".substr($year,2,2).$month.trim(str_pad($row, 3, 0, STR_PAD_LEFT));
        if (self::withTrashed()->where('code', $code)->exists()) {
            $row++;
            return self::checkGenerateCode($row, $year, $month);
        }
        return $code;
    }

    public function requester()
    {
        return $this->belongsTo(User::class,'requested_by','id')->withDefault();
    }

    public function approver()
    {
        return $this->belongsTo(User::class,'approved_by','id')->withDefault();
    }

    public function application()
    {
        return $this->belongsTo(BusinessTripApplication::class,'application_id','id')->withDefault();
    }
}
