<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'pic_name',
        'pic_phone_number',
        'province_id',
        'city_id',
        'district_id',
        'value',
        'description',
        'is_active'
    ];
}
