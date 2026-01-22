<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    public $timestamps = false;

    protected $table = 'payrolls';

    protected $fillable = [
        'department_id',
        'date_from',
        'date_to',
        'status',
        'salary_type',
        'type',
        'project_id',
        'employee_id',
    ];

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class);
    }

    public function getGenerationInfoAttribute()
    {
        if ($this->type === 'individual' && $this->employee) {
            return 'Individual - '.$this->employee->fullname;
        } elseif ($this->type === 'project' && $this->project) {
            return 'Project - '.$this->project->name;
        } elseif ($this->type === 'all' || ! $this->type) {
            return 'Department - '.($this->department->name ?? 'N/A');
        }

        return ucfirst($this->type);
    }

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
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
