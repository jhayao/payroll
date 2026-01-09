<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Position extends Model
{
    public $timestamps = false;
    protected $table = 'positions';
    protected $fillable = [
        'description',
        'daily_rate',
        'hourly_rate',
        'minutely_rate',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }

    public function getFormattedDailyRateAttribute()
    {
        return number_format($this->daily_rate, 2);
    }

    public function getFormattedHourlyRateAttribute()
    {
        return number_format($this->hourly_rate, 2);
    }

    public function getFormattedMinutelyRateAttribute()
    {
        return number_format($this->minutely_rate, 2);
    }
}
