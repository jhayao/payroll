<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Position;
use App\Models\Dtr;
use App\Models\EmployeeShift;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use App\Notifications\EmployeeResetPassword;

class Employee extends Authenticatable
{
    use Notifiable;
    
    public $timestamps = false;
    protected $table = 'employees';
    protected $fillable = [
        'id',
        'lastname',
        'firstname',
        'middlename',
        'suffix',
        'sex',
        'address',
        'mobile_no',
        'position_id',
        'department_id',
        'email',
        'password',
        'remember_token',
        'photo_2x2',
        'photo_lg',
        'purok',
        'barangay',
        'city',
        'employee_id',
        'custom_daily_rate'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    // DTR Helper
    public function dtr()
    {
        return $this->hasMany(Dtr::class, 'employee_id');
    }

    public function dtrRange($from, $to)
    {
        return $this->dtr()
            ->whereBetween('log_date', [$from, $to])
            ->orderBy('log_date', 'asc')
            ->get();
    }

    public function numberOfDutyDays($from, $to)
    {
        return $this->dtrRange($from, $to)->count();
    }

    public function dailyTardiness($log)
    {
        $shift = $this->currentShift()->shift;
        $officialAmIn = $this->parseShift($log->log_date, $shift->am_in);
        $officialAmOut = $this->parseShift($log->log_date, $shift->am_out);
        $officialPmIn = $this->parseShift($log->log_date, $shift->pm_in);
        $officialPmOut = $this->parseShift($log->log_date, $shift->pm_out);

        $tardiness = 0;

        // Only check AM In tardiness if am_in is actually logged
        if ($log->am_in) {
            $logAmIn = Carbon::parse($log->am_in);
            if ($logAmIn->greaterThan($officialAmIn)) {
                $tardiness += $officialAmIn->diffInMinutes($logAmIn);
            }
        }

        // Only check AM Out undertime if am_out is actually logged
        if ($log->am_out) {
            $logAmOut = Carbon::parse($log->am_out);
            if ($logAmOut->lessThan($officialAmOut)) {
                $tardiness += $logAmOut->diffInMinutes($officialAmOut);
            }
        }

        // Only check PM In tardiness if pm_in is actually logged
        if ($log->pm_in) {
            $logPmIn = Carbon::parse($log->pm_in);
            if ($logPmIn->greaterThan($officialPmIn)) {
                $tardiness += $officialPmIn->diffInMinutes($logPmIn);
            }
        }

        // Only check PM Out undertime if pm_out is actually logged
        if ($log->pm_out) {
            $logPmOut = Carbon::parse($log->pm_out);
            if ($logPmOut->lessThan($officialPmOut)) {
                $tardiness += $logPmOut->diffInMinutes($officialPmOut);
            }
        }

        return [
            'tardiness' => $tardiness,
            'hour' => floor($tardiness / 60),
            'minutes' => $tardiness % 60
        ];


    }

    public function tardiness($from, $to)
    {
        $shift = $this->currentShift()->shift;

        $totalTardinessAmIn = 0;
        $totalUndertimeAmOut = 0;
        $totalTardinessPmIn = 0;
        $totalUndertimePmOut = 0;

        $logs = $this->dtrRange($from, $to);

        foreach ($logs as $l) {

            $officialAmIn = $this->parseShift($l->log_date, $shift->am_in);
            $officialAmOut = $this->parseShift($l->log_date, $shift->am_out);
            $officialPmIn = $this->parseShift($l->log_date, $shift->pm_in);
            $officialPmOut = $this->parseShift($l->log_date, $shift->pm_out);

            $logAmIn = Carbon::parse($l->am_in);
            $logAmOut = Carbon::parse($l->am_out);
            $logPmIn = Carbon::parse($l->pm_in);
            $logPmOut = Carbon::parse($l->pm_out);

            $tardinessAmIn = 0;
            if ($logAmIn && $logAmIn->greaterThan($officialAmIn)) {
                $tardinessAmIn = $officialAmIn->diffInMinutes($logAmIn);
            }

            $undertimeAmOut = 0;
            if ($logAmOut && $logAmOut->lessThan($officialAmOut)) {
                $undertimeAmOut = $logAmOut->diffInMinutes($officialAmOut);
            }

            $tardinessPmIn = 0;
            if ($logPmIn && $logPmIn->greaterThan($officialPmIn)) {
                $tardinessPmIn = $officialPmIn->diffInMinutes($logPmIn);
            }

            $undertimePmOut = 0;
            if ($logPmOut && $logPmOut->lessThan($officialPmOut)) {
                $undertimePmOut = $logPmOut->diffInMinutes($officialPmOut);
            }

            // Remove -1 multiplier
            $totalTardinessAmIn += $tardinessAmIn;
            $totalUndertimeAmOut += $undertimeAmOut;
            $totalTardinessPmIn += $tardinessPmIn;
            $totalUndertimePmOut += $undertimePmOut;
        }

        return [
            'totalTardinessAmIn' => $totalTardinessAmIn,
            'totalUndertimeAmOut' => $totalUndertimeAmOut,
            'totalTardinessPmIn' => $totalTardinessPmIn,
            'totalUndertimePmOut' => $totalUndertimePmOut,
            'grandTotal' => $totalTardinessAmIn + $totalUndertimeAmOut + $totalTardinessPmIn + $totalUndertimePmOut,
        ];
    }

    public function overtime($from, $to)
    {
        $logs = $this->dtrRange($from, $to);

        $totalOT = 0;

        foreach ($logs as $l) {

            if (!$l->ot_in || !$l->ot_out) {
                continue; // skip incomplete OT
            }

            $otIn = Carbon::parse($l->ot_in);
            $otOut = Carbon::parse($l->ot_out);

            // If OT crosses midnight, adjust
            if ($otOut->lessThan($otIn)) {
                $otOut->addDay();
            }

            $totalOT += $otIn->diffInMinutes($otOut);
        }

        return $totalOT; // total OT in minutes
    }


    public function dailyOvertime($log)
    {
        if (!$log->ot_in || !$log->ot_out) {
            return 0;
        }

        $otIn = Carbon::parse($log->ot_in);
        $otOut = Carbon::parse($log->ot_out);

        // Handle OT that crosses midnight
        if ($otOut->lessThan($otIn)) {
            $otOut->addDay();
        }

        return $otIn->diffInMinutes($otOut);
    }

    protected function parseShift($date, $time)
    {
        $fullTime = $time . ':00';
        return Carbon::parse($date . ' ' . $fullTime);
    }

    // Shift Helper
    public function shift()
    {
        return $this->hasMany(EmployeeShift::class, 'employee_id');
    }

    public function currentShift()
    {
        return $this->shift()->where('remarks', 'active')->first();
    }

    public function position() 
    {
        return $this->belongsTo(Position::class, 'position_id')->withDefault([
            'description' => 'No Position'
        ]);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->withDefault([
            'name' => 'No Department',
        ]);
    }

    public function allowances()
    {
        return $this->belongsToMany(Allowance::class, 'allowance_employee')->withPivot('amount', 'percentage');
    }

    public function deductions()
    {
        return $this->belongsToMany(Deduction::class, 'deduction_employee')->withPivot('amount', 'percentage');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'employee_project')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'time_keeper_id');
    }

    public function getFullNameAttribute()
    {
        $fullname = trim("{$this->lastname}, {$this->firstname} {$this->middlename}");

        if (!empty($this->suffix)) {
            $fullname .= ' ' . $this->suffix;
        }

        return trim($fullname);
    }
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new EmployeeResetPassword($token));
    }

    public function getDailyRateAttribute()
    {
        return $this->custom_daily_rate ?? $this->position->daily_rate;
    }

    public function getHourlyRateAttribute()
    {
        return $this->daily_rate / 8;
    }

    public function getMinutelyRateAttribute()
    {
        return $this->hourly_rate / 60;
    }

    public function getFormattedDailyRateAttribute()
    {
        return '₱ ' . number_format($this->daily_rate, 2);
    }

}
