<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'time_keeper_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function timeKeeper()
    {
        return $this->belongsTo(Employee::class, 'time_keeper_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_project')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }
}
