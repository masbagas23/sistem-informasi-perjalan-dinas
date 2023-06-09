<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'telephone',
        'gmaps_url',
    ];

    public function applications()
    {
        return $this->hasMany(BusinessTripApplication::class, 'customer_id', 'id');
    }
}
