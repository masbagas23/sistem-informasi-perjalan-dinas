<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseDetail extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_WAITING = 1;
    const STATUS_VALID = 2;
    const STATUS_REJECT = 3;

    protected $fillable = [
        'expense_id',
        'cost_category_id',
        'nominal',
        'description',
        'reason',
        'file_path',
        'approved_by',
        'status',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id')->withDefault();
    }

    public function costCategory()
    {
        return $this->belongsTo(CostCategory::class, 'cost_category_id', 'id')->withDefault();
    }
}
