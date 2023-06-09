<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_id',
        'name',
        'description'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id')->withDefault();
    }
}
