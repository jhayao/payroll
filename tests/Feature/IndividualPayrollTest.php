<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndividualPayrollTest extends TestCase
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
    }

    public function test_can_generate_for_individual_employee()
    {
        $user = User::factory()->create();
        $user->role = 'admin';

        $department = Department::create(['name' => 'IT Department']);
        $position = Position::create(['name' => 'Developer', 'rate' => 1000]);
        
        $employee = Employee::create([
            'lastname' => 'Doe',
            'firstname' => 'John',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'daily_rate' => 1000,
            'rate_type' => 'daily',
            'schedule_type' => 'fixed',
            'hire_date' => now(),
            'mobile_no' => '09123456789'
        ]);

        $shift = \App\Models\Shift::where('name', 'Regular Shift')->first();
        \App\Models\EmployeeShift::create([
            'employee_id' => $employee->id,
            'shift_id' => $shift->id,
            'remarks' => 'active'
        ]);

        $data = [
            'department' => $department->id,
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
            'generation_type' => 'individual',
            'employee_id' => $employee->id
        ];

        $response = $this->actingAs($user)->post(route('payroll.save'), $data);

        $response->assertStatus(302);
        $response->assertSessionHas('status', 'Payroll updated successfully.');

        $payroll = Payroll::first();
        $this->assertNotNull($payroll);
        
        $this->assertDatabaseHas('payroll_items', [
            'payroll_id' => $payroll->id,
            'employee_id' => $employee->id
        ]);
    }

    public function test_can_append_individual_to_existing_payroll()
    {
        $user = User::factory()->create();
        $user->role = 'admin';

        $department = Department::create(['name' => 'IT Department']);
        $position = Position::create(['name' => 'Developer', 'rate' => 1000]);
        
        $emp1 = Employee::create([
            'lastname' => 'Doe',
            'firstname' => 'John',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'daily_rate' => 1000,
            'rate_type' => 'daily',
            'schedule_type' => 'fixed',
            'hire_date' => now(),
            'mobile_no' => '09123456789'
        ]);

        $shift = \App\Models\Shift::where('name', 'Regular Shift')->first();
        \App\Models\EmployeeShift::create([
            'employee_id' => $emp1->id,
            'shift_id' => $shift->id,
            'remarks' => 'active'
        ]);

        $emp2 = Employee::create([
            'lastname' => 'Smith',
            'firstname' => 'Jane',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'daily_rate' => 1000,
            'rate_type' => 'daily',
            'schedule_type' => 'fixed',
            'hire_date' => now(),
            'mobile_no' => '09987654321'
        ]);

        \App\Models\EmployeeShift::create([
            'employee_id' => $emp2->id,
            'shift_id' => $shift->id,
            'remarks' => 'active'
        ]);

        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate = now()->endOfMonth()->format('Y-m-d');

        // Generate for Emp1 first
        $response1 = $this->actingAs($user)->post(route('payroll.save'), [
            'department' => $department->id,
            'date_from' => $startDate,
            'date_to' => $endDate,
            'generation_type' => 'individual',
            'employee_id' => $emp1->id
        ]);

        $response1->assertSessionHas('status', 'Payroll updated successfully.');

        // Generate for Emp2 (should append)
        $response2 = $this->actingAs($user)->post(route('payroll.save'), [
            'department' => $department->id,
            'date_from' => $startDate,
            'date_to' => $endDate,
            'generation_type' => 'individual',
            'employee_id' => $emp2->id
        ]);

        $response2->assertSessionHas('status', 'Payroll updated successfully.');

        $this->assertEquals(1, Payroll::count(), 'Should typically be 1 payroll record.');
        $payroll = Payroll::first();
        $this->assertEquals(2, PayrollItem::where('payroll_id', $payroll->id)->count(), 'Should have 2 items.');
    }

    public function test_generation_type_all_works()
    {
         $user = User::factory()->create();
        $user->role = 'admin';

        $department = Department::create(['name' => 'IT Department']);
        $position = Position::create(['name' => 'Developer', 'rate' => 1000]);
        
        $employee = Employee::create([
            'lastname' => 'Doe',
            'firstname' => 'John',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'daily_rate' => 1000,
            'rate_type' => 'daily',
            'schedule_type' => 'fixed',
            'hire_date' => now(),
            'mobile_no' => '09123456789'
        ]);

        $shift = \App\Models\Shift::where('name', 'Regular Shift')->first();
        \App\Models\EmployeeShift::create([
            'employee_id' => $employee->id,
            'shift_id' => $shift->id,
            'remarks' => 'active'
        ]);

        $response = $this->actingAs($user)->post(route('payroll.save'), [
            'department' => $department->id,
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
            'generation_type' => 'all'
        ]);
        
        $response->assertSessionHas('status'); // Might be updated or created message
        $payroll = Payroll::first();
        $this->assertEquals(1, PayrollItem::where('payroll_id', $payroll->id)->count());
    }
}
