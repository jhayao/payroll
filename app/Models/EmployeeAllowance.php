<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllowance extends Model
{
    public $timestamps = false;
    protected $table = 'employee_allowances';
    protected $fillable = [
        'payroll_item_id',
        'description',
        'amount'
    ];

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }
}
