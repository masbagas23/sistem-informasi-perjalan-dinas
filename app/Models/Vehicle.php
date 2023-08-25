<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'condition',
        'number_plate_license',
    ];

    public function loans()
    {
        return $this->hasMany(VehicleLoan::class, 'vehicle_id', 'id');
    }
}
