<?php

use App\Models\Deduction;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Models\Shift;
use App\Models\EmployeeShift;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Basic setup often needed for payroll calculations
    Shift::create([
        'name' => 'Regular Shift',
        'am_in' => '08:00',
        'am_out' => '12:00',
        'pm_in' => '13:00',
        'pm_out' => '17:00',
    ]);
});

test('deduction can be created with every_payroll schedule', function () {
    $user = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($user)->post(route('payroll.deductions.save'), [
        'description' => 'Test Deduction',
        'type' => 'fixed',
        'scope' => 'all',
        'amount' => 500,
        'schedule' => 'every_payroll',
    ]);

    $response->assertRedirect(route('payroll.deductions'));
    $response->assertSessionHas('status', 'Saved.');

    $this->assertDatabaseHas('deductions', [
        'description' => 'Test Deduction',
        'schedule' => 'every_payroll',
    ]);
});

test('deduction can be updated', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $deduction = Deduction::create([
        'description' => 'Old Deduction',
        'type' => 'fixed',
        'scope' => 'all',
        'amount' => 100,
        'schedule' => 'every_payroll',
    ]);

    $response = $this->actingAs($user)->post(route('payroll.deductions.update', $deduction->id), [
        'description' => 'Updated Deduction',
        'type' => 'fixed',
        'scope' => 'all',
        'amount' => 200,
        'schedule' => 'every_payroll',
    ]);

    $response->assertRedirect(route('payroll.deductions'));
    // Based on previous findings, update usually redirects with 'status' => 'Updated.' or similar.
    // If unsure, we can assertSessionHasNoErrors first.
    // Let's assume 'Updated.' based on typical controller pattern I saw in Allowances.
    // But PayrollController.php was truncated before updateDeduction return. 
    // Let's just check no errors and database match.
    $response->assertSessionHasNoErrors();
    
    $this->assertDatabaseHas('deductions', [
        'id' => $deduction->id,
        'description' => 'Updated Deduction',
        'amount' => 200,
    ]);
});

test('payroll generation works with deductions', function () {
    $user = User::factory()->create(['role' => 'admin']);

    // Setup Data
    $department = Department::create(['name' => 'IT Department']);
    $position = Position::create(['name' => 'Developer', 'rate' => 1000]); // 'rate' might be daily_rate? Position migration?
    // In IndividualPayrollTest: 'daily_rate' => 1000 is on EMPLOYEE. Position has 'rate'.
    
    $employee = Employee::create([
        'lastname' => 'User',
        'firstname' => 'Test',
        'middlename' => 'T',
        'sex' => 'Male',
        'position_id' => $position->id,
        'employee_id' => 'EMP-TEST',
        'department_id' => $department->id,
        'daily_rate' => 1000,
        'minutely_rate' => 2,
        'rate_type' => 'daily', // Required by some logic likely
        'schedule_type' => 'fixed',
        'hire_date' => now(),
    ]);

    // Assign Shift
    $shift = Shift::first();
    EmployeeShift::create([
        'employee_id' => $employee->id,
        'shift_id' => $shift->id,
        'remarks' => 'active'
    ]);

    // Create a Deduction
    $deduction = Deduction::create([
        'description' => 'Health Insurance',
        'type' => 'fixed',
        'scope' => 'all', 
        'amount' => 100,
        'schedule' => 'every_payroll',
    ]);

    // Create Payroll
    $response = $this->actingAs($user)->post(route('payroll.save'), [
        'department' => $department->id,
        'date_from' => now()->startOfMonth()->format('Y-m-d'),
        'date_to' => now()->endOfMonth()->format('Y-m-d'),
        'generation_type' => 'all',
    ]);
    
    $response->assertSessionHasNoErrors();
    $response->assertStatus(302); // Redirect
    
    // Verify Payroll Item
    $payroll = Payroll::first();
    expect($payroll)->not->toBeNull();
    
    $item = $payroll->items->first();
    expect($item)->not->toBeNull();
    expect($item->employee_id)->toBe($employee->id);
    
    // Verify Deduction was applied
    // The relationship is likely $item->deductions (EmployeeDeduction model?)
    // Let's check EmployeeDeduction.php if needed, or check logic in Controller:
    // EmployeeDeduction::create(['payroll_item_id' => ... ])
    // PayrollItem hasMany employeeDeductions?
    // Let's check PayrollItem model or assume typical naming
    // Controller calls: EmployeeDeduction::create
    
    $appliedDeduction = \App\Models\EmployeeDeduction::where('payroll_item_id', $item->id)
        ->where('description', 'Health Insurance')
        ->first();
        
    expect($appliedDeduction)->not->toBeNull();
    expect((float)$appliedDeduction->amount)->toBe(100.00);
});
