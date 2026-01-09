<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    public $timestamps = false;
    protected $table = 'deductions';
    protected $fillable = [
        'description'
    ];
}
