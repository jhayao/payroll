<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use Carbon\Carbon;

class Dtr extends Model
{
    public $timestamps = false;
    protected $table = 'dtr';
    protected $fillable = [
        'employee_id',
        'log_date',
        'am_in',
        'am_out',
        'pm_in',
        'pm_out',
        'ot_in',
        'ot_out'
    ];

    protected $casts = [
        'am_in' => 'datetime',
        'am_out' => 'datetime',
        'pm_in' => 'datetime',
        'pm_out' => 'datetime',
        'ot_in' => 'datetime',
        'ot_out' => 'datetime',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // --- Accessors (automatic formatting when getting values) ---
    public function getFormattedAmInAttribute()
    {
        return $this->am_in ? Carbon::parse($this->am_in)->format('h:i A') : null;
    }

    public function getFormattedAmOutAttribute()
    {
        return $this->am_out ? Carbon::parse($this->am_out)->format('h:i A') : null;
    }

    public function getFormattedPmInAttribute()
    {
        return $this->pm_in ? Carbon::parse($this->pm_in)->format('h:i A') : null;
    }

    public function getFormattedPmOutAttribute()
    {
        return $this->pm_out ? Carbon::parse($this->pm_out)->format('h:i A') : null;
    }

    public function getFormattedOtInAttribute()
    {
        return $this->ot_in ? Carbon::parse($this->ot_in)->format('h:i A') : null;
    }

    public function getFormattedOtOutAttribute()
    {
        return $this->ot_out ? Carbon::parse($this->ot_out)->format('h:i A') : null;
    }

    // --- Mutators (automatic formatting when saving values) ---
    public function setAmInAttribute($value)
    {
        $this->attributes['am_in'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:00') : null;
    }

    public function setAmOutAttribute($value)
    {
        $this->attributes['am_out'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:00') : null;
    }

    public function setPmInAttribute($value)
    {
        $this->attributes['pm_in'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:00') : null;
    }

    public function setPmOutAttribute($value)
    {
        $this->attributes['pm_out'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:00') : null;
    }

    public function setOtInAttribute($value)
    {
        $this->attributes['ot_in'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:00') : null;
    }

    public function setOtOutAttribute($value)
    {
        $this->attributes['ot_out'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:00') : null;
    }
}
