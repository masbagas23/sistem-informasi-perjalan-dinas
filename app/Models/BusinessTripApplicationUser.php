<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessTripApplicationUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'user_id',
        'is_leader',
    ];

    public function application()
    {
        return $this->belongsTo(BusinessTripApplication::class,'application_id','id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id')->withDefault();
    }
}
