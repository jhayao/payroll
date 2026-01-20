<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDeduction;

class PayrollItem extends Model
{
    public $timestamps = false;
    protected $table = 'payroll_items';
    protected $fillable = [
        'payroll_id',
        'employee_id',
        'date_from',
        'date_to',
        'num_of_days',
        'daily_rate',
        'overtime',
        'overtime_pay',
        'undertime_minutes',
        'undertime_amount',
        'gross_pay',
        'net_pay'
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function allowances()
    {
        return $this->hasMany(EmployeeAllowance::class, 'payroll_item_id');
    }

    public function totalAllowance()
    {
        return $this->allowances()->sum('amount');
    }

    public function deductions()
    {
        return $this->hasMany(EmployeeDeduction::class, 'payroll_item_id');
    }

    public function totalDeduction()
    {
        return $this->deductions()->sum('amount');
    }

    public function netPay()
    {
        return $this->gross_pay + $this->totalAllowance() - $this->totalDeduction() - $this->undertime_amount;
    }

    public function getFormattedTotalAllowanceAttribute()
    {
        return number_format($this->totalAllowance(), 2);
    }

    public function getFormattedTotalDeductionAttribute()
    {
        return number_format($this->totalDeduction(), 2);
    }

    public function getFormattedNetPayAttribute()
    {
        return number_format($this->netPay(), 2);
    }

    public function getFormattedDailyRateAttribute()
    {
        return number_format($this->daily_rate, 2);
    }

    public function getFormattedGrossPayAttribute()
    {
        return number_format($this->gross_pay, 2);
    }

    public function getFormattedOvertimePayAttribute()
    {
        return number_format($this->overtime_pay, 2);
    }

}