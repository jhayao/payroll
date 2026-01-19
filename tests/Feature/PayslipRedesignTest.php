<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Position;
use App\Models\Shift;
use App\Models\Dtr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class PayslipRedesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->department = Department::create(['name' => 'IT Department']);
        $this->position = Position::create(['description' => 'Developer', 'daily_rate' => 1000]);
        
        // Define shift: 8:00 - 17:00 (9 hours total with break? assuming 8 working hours)
        // DTR logic usually calculates lateness based on this.
        $this->shift = Shift::create([
            'name' => 'Regular Shift',
            'am_in' => '08:00',
            'am_out' => '12:00',
            'pm_in' => '13:00',
            'pm_out' => '17:00',
        ]);
        
        $this->employee = Employee::create([
            'lastname' => 'Doe',
            'firstname' => 'John',
            'department_id' => $this->department->id,
            'position_id' => $this->position->id,
            'daily_rate' => 1000,
            'hourly_rate' => 1000 / 8,
            'minutely_rate' => (1000 / 8) / 60,
            'rate_type' => 'daily',
            'schedule_type' => 'fixed',
            'hire_date' => now(),
            'mobile_no' => '09123456789',
            'salary_type' => 'weekly'
        ]);

        \App\Models\EmployeeShift::create([
            'employee_id' => $this->employee->id,
            'shift_id' => $this->shift->id,
            'remarks' => 'active'
        ]);
    }

    public function test_undertime_is_calculated_and_stored()
    {
        // 1. Create DTR with Lateness/Undertime
        // Shift starts at 08:00. Employee creates time in at 09:00 (60 mins late).
        $date = Carbon::parse('2026-01-05'); // Monday
        Dtr::create([
            'employee_id' => $this->employee->id,
            'log_date' => $date->format('Y-m-d'),
            'am_in' => $date->format('Y-m-d') . ' 09:00:00', // 1 hour late
            'am_out' => $date->format('Y-m-d') . ' 12:00:00',
            'pm_in' => $date->format('Y-m-d') . ' 13:00:00',
            'pm_out' => $date->format('Y-m-d') . ' 17:00:00'
        ]);

        // 2. Generate Payroll
        $response = $this->actingAs($this->user)->post(route('payroll.save'), [
            'department' => $this->department->id,
            'date_from' => $date->startOfWeek()->format('Y-m-d'), // 2026-01-05
            'date_to' => $date->endOfWeek()->format('Y-m-d'),   // 2026-01-11
            'generation_type' => 'all',
        ]);

        $response->assertSessionHas('status', 'Payroll updated successfully.');

        // 3. Verify PayrollItem
        $item = PayrollItem::where('employee_id', $this->employee->id)->first();
        
        $this->assertNotNull($item);
        
        // Expected Undertime: 60 minutes
        $this->assertEquals(60, $item->undertime_minutes, 'Undertime minutes should be 60');
        
        // Expected Deduction Amount: 60 mins * minutely_rate (1000 / 8 / 60 = 2.0833) = ~125
        $rate = 1000 / 8 / 60;
        $expectedAmount = 60 * $rate;
        
        $this->assertEqualsWithDelta($expectedAmount, $item->undertime_amount, 0.01, 'Undertime amount mismatch');
        
        // Verify Net Pay Calculation logic in model
        // Net Pay = Gross (1 day = 1000) - Undertime (125) = 875
        // Note: Gross is based on attendance. If present, he gets daily rate (1000). Then tardiness is deducted.
        // Assuming Logic: Gross = NumDays * DailyRate (plus OT).
        // Since he was present (despite late), NumDays should be 1?
        // Let's check logic: $numDays = $e->numberOfDutyDays...
        // If he checks in, is it 1 day or partial? Usually 1 day if present, then tardiness deducted separately.
        
        $this->assertEquals(1, $item->num_of_days, 'Should be counted as 1 day present');
        $this->assertEqualsWithDelta(1000, $item->gross_pay, 0.01, 'Gross pay should be full daily rate');
        
        // Net Pay = Gross - Undertime
        $this->assertEqualsWithDelta(875, $item->net_pay, 0.01, 'Net Pay should be Gross - Undertime');
    }
}
