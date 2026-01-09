<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Shift;

class EmployeeShift extends Model
{
    public $timestamps = false;
    protected $table = 'employee_shifts';
    protected $fillable = [
        'employee_id',
        'shift_id',
        'remarks'
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
    
}
