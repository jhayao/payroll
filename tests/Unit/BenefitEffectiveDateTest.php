<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\Allowance;
use App\Models\PayrollItem;
use App\Models\EmployeeAllowance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class BenefitEffectiveDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_allowance_specific_year_application()
    {
        // 1. Setup
        $dept = Department::create(['name' => 'IT', 'abbr' => 'IT']);
        $employee = Employee::factory()->create(['department_id' => $dept->id, 'salary_type' => 'weekly']);

        // Create Allowance for January 2026
        $allowance2026 = Allowance::create([
            'description' => '2026 Bonus',
            'type' => 'fixed',
            'scope' => 'all',
            'amount' => 1000,
            'schedule' => 'specific_month',
            'target_month' => 1, // January
            'target_year' => 2026
        ]);

        // Create Allowance for January (Any Year)
        $allowanceAnnual = Allowance::create([
            'description' => 'Annual Bonus',
            'type' => 'fixed',
            'scope' => 'all',
            'amount' => 500,
            'schedule' => 'specific_month',
            'target_month' => 1 // January
        ]);

        // 2. Test Payroll in Jan 2026 (Should get BOTH)
        $payroll2026 = Payroll::create([
            'department_id' => $dept->id,
            'date_from' => '2026-01-01',
            'date_to' => '2026-01-15',
            'type' => 'all'
        ]);
        
        // Trigger calculation (simulating controller logic)
        // We can call the controller logic or just invoke the route if we want integration test.
        // Let's us route since logic is in private method in controller.
        
        $response = $this->post(route('payroll.save'), [
             'department' => $dept->id,
             'date_from' => '2026-01-16', // Another period in Jan 2026 to avoid duplicate check on $payroll2026 if I reused it incorrectly, 
             // actually let's just make a new payroll via the endpoint
             'date_to' => '2026-01-31',
             'generation_type' => 'all',
             'salary_type' => 'weekly'
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $payrollGenerated = Payroll::orderBy('id', 'desc')->first();
        $this->assertNotNull($payrollGenerated, 'Payroll was not created');
        
        // Debug: check if item creation loop ran
        // Ideally we check logs but here we inspect DB
        $totalItems = PayrollItem::where('payroll_id', $payrollGenerated->id)->count();
        $this->assertEquals(1, $totalItems, 'Should create 1 payroll item for the employee');

        $item = PayrollItem::where('payroll_id', $payrollGenerated->id)->where('employee_id', $employee->id)->first();
        
        $this->assertNotNull($item);
        
        // Check Allowances
        $has2026 = EmployeeAllowance::where('payroll_item_id', $item->id)->where('description', '2026 Bonus')->exists();
        $hasAnnual = EmployeeAllowance::where('payroll_item_id', $item->id)->where('description', 'Annual Bonus')->exists();
        
        $this->assertTrue($has2026, 'Should have 2026 Bonus in Jan 2026');
        $this->assertTrue($hasAnnual, 'Should have Annual Bonus in Jan 2026');


        // 3. Test Payroll in Jan 2025 (Should get ONLY Annual)
        $this->post(route('payroll.save'), [
             'department' => $dept->id,
             'date_from' => '2025-01-01', 
             'date_to' => '2025-01-15',
             'generation_type' => 'all'
        ]);

        $payroll2025 = Payroll::orderBy('id', 'desc')->first();
        $item2025 = PayrollItem::where('payroll_id', $payroll2025->id)->where('employee_id', $employee->id)->first();
        
        $has2026In2025 = EmployeeAllowance::where('payroll_item_id', $item2025->id)->where('description', '2026 Bonus')->exists();
        $hasAnnualIn2025 = EmployeeAllowance::where('payroll_item_id', $item2025->id)->where('description', 'Annual Bonus')->exists();
        
        $this->assertFalse($has2026In2025, 'Should NOT have 2026 Bonus in Jan 2025');
        $this->assertTrue($hasAnnualIn2025, 'Should have Annual Bonus in Jan 2025');
    }
}
