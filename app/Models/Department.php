<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Department extends Model
{
    public $timestamps = false;
    protected $table = 'departments';
    protected $fillable = [
        'name',
        'abbr',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }
}
