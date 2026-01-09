<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    public $timestamps = false;
    protected $table = 'shifts';
    protected $fillable = [
        'name',
        'am_in',
        'am_out',
        'pm_in',
        'pm_out',
        'in_out_interval',
        'out_in_interval',
        'is_holiday',
        'rate_percentage'
    ];

    public function getFormattedAmInAttribute()
    {
        $datetime = Carbon::parse(Carbon::today()->format('Y-m-d') . ' ' . $this->am_in);
        return $datetime->format('h:i A');
    }

    public function getFormattedAmOutAttribute()
    {
        $datetime = Carbon::parse(Carbon::today()->format('Y-m-d') . ' ' . $this->am_out);
        return $datetime->format('h:i A');
    }

    public function getFormattedPmInAttribute()
    {
        $datetime = Carbon::parse(Carbon::today()->format('Y-m-d') . ' ' . $this->pm_in);
        return $datetime->format('h:i A');
    }

    public function getFormattedPmOutAttribute()
    {
        $datetime = Carbon::parse(Carbon::today()->format('Y-m-d') . ' ' . $this->pm_out);
        return $datetime->format('h:i A');
    }

}
