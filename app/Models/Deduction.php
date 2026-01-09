<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    public $timestamps = false;
    protected $table = 'deductions';
    protected $fillable = [
        'description', 'type', 'scope', 'amount', 'percentage', 'schedule', 'target_month'
    ];

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'deduction_position')
            ->withPivot(['amount', 'percentage'])
            ->withTimestamps();
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'deduction_employee')
            ->withPivot(['amount', 'percentage']);
    }
}
