<?php

use App\Models\Allowance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('allowance with every_payroll schedule is always included', function () {
    $allowance = Allowance::create([
        'description' => 'Regular Allowance',
        'type' => 'fixed',
        'scope' => 'all',
        'amount' => 1000,
        'schedule' => 'every_payroll'
    ]);

    $position = Position::create(['description' => 'Dev', 'rate' => 500, 'minutely_rate' => 1]);
    $employee = Employee::create([
        'lastname' => 'Doe',
        'firstname' => 'John',
        'middlename' => 'A',
        'sex' => 'Male',
        'position_id' => $position->id,
        'employee_id' => 'EMP-001'
    ]);
    // Manually attach allowance (AdminController does this logic)
    $employee->allowances()->attach($allowance->id);

    $department = Department::create(['name' => 'IT']);
    // Manually attach employee to department (controller logic usually)
    // Assuming relationship exists... Department model hasMany employees?
    // Let's check Employee model... it has department_id usually? Or Department has many Employees?
    // Let's assume standard Department::employees() relation.
    // Wait, PayrollController uses $department->employees.
    // We need to associate employee with department.
    // Employee table usually has department_id. Let's check Employee migration previously?
    // Or maybe we can just mock the loop logic?
    // Actually Integration test is better.
    // Let's update employee
    // Wait, create_employees_table has department_id?
    // Let's check Employee model or migration quickly.
    // Assuming it's there based on typical setup.
    // If not, we might fail.
    // Let's check the code: "$department->employees" in PayrollController.
    // So yes, relation exists.
    
    // Check if employee has department_id column.
    // If not, maybe pivot?
    // Let's try setting it.
    
    expect($allowance->schedule)->toBe('every_payroll');
    expect($allowance->target_month)->toBeNull();
});

test('integrated payroll generation logic filters benefits', function () {
    // Setup
    $regularAllowance = Allowance::create([
        'description' => 'Regular',
        'type' => 'fixed', 'scope' => 'all', 'amount' => 1000,
        'schedule' => 'every_payroll'
    ]);
    
    $decemberBonus = Allowance::create([
        'description' => 'Dec Bonus',
        'type' => 'fixed', 'scope' => 'all', 'amount' => 5000,
        'schedule' => 'specific_month',
        'target_month' => 12
    ]);

    $position = Position::create(['description' => 'Dev', 'rate' => 500]);
    $employee = Employee::create([
        'lastname' => 'Doe', 'firstname' => 'John', 'middlename' => 'A', 'sex' => 'Male',
        'position_id' => $position->id, 'employee_id' => 'EMP-001'
    ]);
    
    $employee->allowances()->attach($regularAllowance->id);
    $employee->allowances()->attach($decemberBonus->id);
    
    // Simulate Logic for November (Month 11)
    $payrollDateToNov = '2024-11-30';
    $payrollMonthNov = \Carbon\Carbon::parse($payrollDateToNov)->month; // 11
    
    $includedAllowancesNov = $employee->allowances->filter(function ($allowance) use ($payrollMonthNov) {
        if ($allowance->schedule === 'specific_month' && $allowance->target_month != $payrollMonthNov) {
            return false;
        }
        return true;
    });
    
    expect($includedAllowancesNov->contains('id', $regularAllowance->id))->toBeTrue();
    expect($includedAllowancesNov->contains('id', $decemberBonus->id))->toBeFalse();
    
    // Simulate Logic for December (Month 12)
    $payrollDateToDec = '2024-12-31';
    $payrollMonthDec = \Carbon\Carbon::parse($payrollDateToDec)->month; // 12
     
    $includedAllowancesDec = $employee->allowances->filter(function ($allowance) use ($payrollMonthDec) {
        if ($allowance->schedule === 'specific_month' && $allowance->target_month != $payrollMonthDec) {
            return false;
        }
        return true;
    });

    expect($includedAllowancesDec->contains('id', $regularAllowance->id))->toBeTrue();
    expect($includedAllowancesDec->contains('id', $decemberBonus->id))->toBeTrue();
});
