<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessTripApplication extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_WAITING = 1;
    const STATUS_APPROVE = 2;
    const STATUS_CANCEL = 3;
    const STATUS_REJECT = 4;

    const RESULT_ON_PROGRESS = 1;
    const RESULT_DONE = 2;
    const RESULT_PENDING = 3;
    const RESULT_RESCHEDULE = 4;

    protected $fillable = [
        'code',
        'code_letter',
        'customer_id',
        'job_category_id',
        'start_date',
        'end_date',
        'note',
        'requested_by',
        'approved_by',
        'status',
        'result',
        'total_day',
        'description',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withDefault();
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id')->withDefault();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id')->withDefault();
    }

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id', 'id')->withDefault();
    }

    public function users()
    {
        return $this->hasMany(BusinessTripApplicationUser::class, 'application_id', 'id')->orderBy('is_leader', 'DESC');
    }

    public function targets()
    {
        return $this->hasMany(BusinessTripApplicationTarget::class, 'application_id', 'id');
    }

    public static function generateCodeLetter($date)
    {
        $month      = substr($date, 5, 2);
        $year       = substr($date, 0, 4);
        $lastRow    = self::withTrashed()
        ->whereMonth('created_at',$month)
        ->whereYear('created_at',$year)
        ->count() + 1;
        return self::checkGenerateCodeLetter($lastRow, $year, $month);
    }

    public static function checkGenerateCodeLetter($row, $year, $month)
    {

        $code  = trim(str_pad($row, 3, 0, STR_PAD_LEFT)."/FS/SPD/". monthRomawi($month)."/". $year);
        if (self::withTrashed()->where('code_letter', $code)->exists()) {
            $row++;
            return self::checkGenerateCode($row, $year, $month);
        }
        return $code;
    }

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

        $code  = trim(str_pad("SPD".substr($year,2,2).$month.$row, 3, 0, STR_PAD_LEFT));
        if (self::withTrashed()->where('code', $code)->exists()) {
            $row++;
            return self::checkGenerateCode($row, $year, $month);
        }
        return $code;
    }
}
