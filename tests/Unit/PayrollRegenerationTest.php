<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollRegenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regeneration_respects_salary_type()
    {
        // 1. Setup
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $dept = Department::create(['name' => 'IT', 'abbr' => 'IT']);
        $shift = \App\Models\Shift::create([
            'name' => 'Regular',
            'am_in' => '08:00',
            'am_out' => '12:00',
            'pm_in' => '13:00',
            'pm_out' => '17:00'
        ]);

        // Emp 1: Weekly
        $empWeekly = Employee::factory()->create([
            'department_id' => $dept->id,
            'salary_type' => 'weekly',
            'custom_daily_rate' => 500
        ]);
        \App\Models\EmployeeShift::create([
            'employee_id' => $empWeekly->id,
            'shift_id' => $shift->id,
            'remarks' => 'active'
        ]);

        // Emp 2: Semi-monthly
        $empSemi = Employee::factory()->create([
            'department_id' => $dept->id,
            'salary_type' => 'semi_monthly',
            'custom_daily_rate' => 600
        ]);
        \App\Models\EmployeeShift::create([
            'employee_id' => $empSemi->id,
            'shift_id' => $shift->id,
            'remarks' => 'active'
        ]);

        // 2. Generate Payroll for 'weekly' only
        $dateFrom = '2024-01-01';
        $dateTo = '2024-01-07';

        $response = $this->post(route('payroll.save'), [
            'department' => $dept->id,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'generation_type' => 'all',
            'salary_type' => 'weekly'
        ]);
        
        if ($response->exception) {
             dump($response->exception->getMessage());
        }
        $response->assertSessionHasNoErrors();

        $payroll = Payroll::first();
        $this->assertNotNull($payroll);

        // Assert only inclusive of weekly employees
        $this->assertTrue(PayrollItem::where('payroll_id', $payroll->id)->where('employee_id', $empWeekly->id)->exists(), 'Weekly employee should be in payroll');
        $this->assertFalse(PayrollItem::where('payroll_id', $payroll->id)->where('employee_id', $empSemi->id)->exists(), 'Semi-monthly employee should NOT be in payroll');

        // 3. Regenerate
        $this->post(route('payroll.regenerate', $payroll->id));

        // 4. Assert again - THIS IS EXPECTED TO FAIL CURRENTLY
        // Upon regeneration, strict filtering should presumably apply if we implement the fix.
        // Currently, it re-adds everyone.
        $this->assertTrue(PayrollItem::where('payroll_id', $payroll->id)->where('employee_id', $empWeekly->id)->exists(), 'Weekly employee should remain');
        $this->assertFalse(PayrollItem::where('payroll_id', $payroll->id)->where('employee_id', $empSemi->id)->exists(), 'Semi-monthly employee should NOT appear after regeneration');
    }
}
