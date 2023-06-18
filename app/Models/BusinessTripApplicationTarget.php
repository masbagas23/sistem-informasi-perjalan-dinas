<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessTripApplicationTarget extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_WAITING = 1;
    const STATUS_PENDING = 2;
    const STATUS_DONE = 3;

    protected $fillable = [
        'application_id',
        'name',
        'description',
        'duration',
        'start_date',
        'end_date',
        'status',
        'file_path',
        'reason'
    ];

    public function application()
    {
        return $this->belongsTo(BusinessTripApplication::class, 'application_id', 'id')->withDefault();
    }
}
