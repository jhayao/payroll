<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollSalaryTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \App\Models\Shift::create([
            'name' => 'Regular Shift',
            'am_in' => '08:00',
            'am_out' => '12:00',
            'pm_in' => '13:00',
            'pm_out' => '17:00',
        ]);
        
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->department = Department::create(['name' => 'IT Department']);
        $this->position = Position::create(['name' => 'Developer', 'rate' => 1000]);
        $this->shift = \App\Models\Shift::where('name', 'Regular Shift')->first();
    }

    private function createEmployee($type, $firstname)
    {
        $employee = Employee::create([
            'lastname' => 'Doe',
            'firstname' => $firstname,
            'department_id' => $this->department->id,
            'position_id' => $this->position->id,
            'daily_rate' => 1000,
            'rate_type' => 'daily',
            'schedule_type' => 'fixed',
            'hire_date' => now(),
            'mobile_no' => '09123456789',
            'salary_type' => $type
        ]);

        \App\Models\EmployeeShift::create([
            'employee_id' => $employee->id,
            'shift_id' => $this->shift->id,
            'remarks' => 'active'
        ]);

        return $employee;
    }

    public function test_can_generate_for_weekly_employees_only()
    {
        $weeklyEmp = $this->createEmployee('weekly', 'WeeklyGuy');
        $semiEmp = $this->createEmployee('semi_monthly', 'SemiGuy');

        $response = $this->actingAs($this->user)->post(route('payroll.save'), [
            'department' => $this->department->id,
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
            'generation_type' => 'all',
            'salary_type' => 'weekly'
        ]);

        $response->assertSessionHas('status', 'Payroll updated successfully.');

        $payroll = Payroll::first();
        $this->assertDatabaseHas('payroll_items', [
            'payroll_id' => $payroll->id,
            'employee_id' => $weeklyEmp->id
        ]);
        $this->assertDatabaseMissing('payroll_items', [
            'payroll_id' => $payroll->id,
            'employee_id' => $semiEmp->id
        ]);
    }

    public function test_can_generate_for_semi_monthly_employees_only()
    {
        $weeklyEmp = $this->createEmployee('weekly', 'WeeklyGuy');
        $semiEmp = $this->createEmployee('semi_monthly', 'SemiGuy');

        $response = $this->actingAs($this->user)->post(route('payroll.save'), [
            'department' => $this->department->id,
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
            'generation_type' => 'all',
            'salary_type' => 'semi_monthly'
        ]);

        $response->assertSessionHas('status', 'Payroll updated successfully.');

        $payroll = Payroll::first();
        $this->assertDatabaseHas('payroll_items', [
            'payroll_id' => $payroll->id,
            'employee_id' => $semiEmp->id
        ]);
        $this->assertDatabaseMissing('payroll_items', [
            'payroll_id' => $payroll->id,
            'employee_id' => $weeklyEmp->id
        ]);
    }

    public function test_can_generate_for_all_types_if_filter_empty()
    {
        $weeklyEmp = $this->createEmployee('weekly', 'WeeklyGuy');
        $semiEmp = $this->createEmployee('semi_monthly', 'SemiGuy');

        $response = $this->actingAs($this->user)->post(route('payroll.save'), [
            'department' => $this->department->id,
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
            'generation_type' => 'all',
            'salary_type' => null
        ]);

        $response->assertSessionHas('status', 'Payroll updated successfully.');

        $payroll = Payroll::first();
        $this->assertEquals(2, PayrollItem::where('payroll_id', $payroll->id)->count());
    }
    public function test_individual_generation_ignores_salary_type_param()
    {
        $weeklyEmp = $this->createEmployee('weekly', 'WeeklyGuy');

        $response = $this->actingAs($this->user)->post(route('payroll.save'), [
            'department' => $this->department->id,
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
            'generation_type' => 'individual',
            'salary_type' => 'semi_monthly', // Mismatch, but should be ignored
            'employee_id' => $weeklyEmp->id
        ]);

        $response->assertSessionHas('status', 'Payroll updated successfully.');
        
        $payroll = Payroll::first();
        $this->assertDatabaseHas('payroll_items', [
            'payroll_id' => $payroll->id,
            'employee_id' => $weeklyEmp->id
        ]);
    }
}
