<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    public $timestamps = false;
    protected $table = 'allowances';
    protected $fillable = [
        'description',
        'type',
        'scope',
        'amount',
        'percentage',
        'schedule',
        'target_month',
        'target_year'
    ];

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'allowance_position')
            ->withPivot(['amount', 'percentage'])
            ->withTimestamps();
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'allowance_employee')
            ->withPivot(['amount', 'percentage']);
    }

}
