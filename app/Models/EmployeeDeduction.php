<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    public $timestamps = false;
    protected $table = 'employee_deductions';
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
