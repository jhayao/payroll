<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PayrollItem;
use App\Models\Department;
use Carbon\Carbon;

class Payroll extends Model
{
    public $timestamps = false;
    protected $table = 'payrolls';
    protected $fillable = [
        'department_id',
        'date_from',
        'date_to',
        'status'
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to'   => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PayrollItem::class, 'payroll_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getFormattedDateFromAttribute()
    {
        return Carbon::parse($this->date_from)->format('F d, Y');
    }

    public function getFormattedDateToAttribute()
    {
        return Carbon::parse($this->date_to)->format('F d, Y');
    }
}
