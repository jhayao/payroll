<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    public $timestamps = false;
    protected $table = 'allowances';
    protected $fillable = [
        'description'
    ];

}
