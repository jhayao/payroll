<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollProjectGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_payroll_by_project()
    {
        // 1. Setup
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $dept = Department::create(['name' => 'IT', 'abbr' => 'IT']);
        
        // Setup shift
        $shift = \App\Models\Shift::create([
            'name' => 'Regular',
            'am_in' => '08:00',
            'am_out' => '12:00',
            'pm_in' => '13:00',
            'pm_out' => '17:00'
        ]);

        // Emp A (IT, In Project)
        $empA = Employee::factory()->create(['department_id' => $dept->id, 'salary_type' => 'weekly', 'custom_daily_rate' => 500]);
        \App\Models\EmployeeShift::create(['employee_id' => $empA->id, 'shift_id' => $shift->id, 'remarks' => 'active']);

        // Emp B (IT, NOT In Project)
        $empB = Employee::factory()->create(['department_id' => $dept->id, 'salary_type' => 'weekly', 'custom_daily_rate' => 500]);
        \App\Models\EmployeeShift::create(['employee_id' => $empB->id, 'shift_id' => $shift->id, 'remarks' => 'active']);

        // Emp C (Other Dept, In Project - Should be excluded by department filter as well)
        $otherDept = Department::create(['name' => 'HR', 'abbr' => 'HR']);
        $empC = Employee::factory()->create(['department_id' => $otherDept->id, 'salary_type' => 'weekly', 'custom_daily_rate' => 500]);
        \App\Models\EmployeeShift::create(['employee_id' => $empC->id, 'shift_id' => $shift->id, 'remarks' => 'active']);


        $project = Project::create(['name' => 'Project Alpha', 'status' => 'active']);
        $project->employees()->attach([$empA->id, $empC->id]);

        // 2. Generate Payroll for Project Alpha
        $response = $this->post(route('payroll.save'), [
            'department' => $dept->id,
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-07',
            'generation_type' => 'project',
            'project_id' => $project->id,
            'salary_type' => 'weekly'
        ]);
        
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $payroll = Payroll::first();
        $this->assertNotNull($payroll);

        // 3. Verify Only Emp A is included
        $this->assertTrue(PayrollItem::where('payroll_id', $payroll->id)->where('employee_id', $empA->id)->exists(), 'Emp A (In Project) should be included');
        $this->assertFalse(PayrollItem::where('payroll_id', $payroll->id)->where('employee_id', $empB->id)->exists(), 'Emp B (Not Project) should NOT be included');
        $this->assertFalse(PayrollItem::where('payroll_id', $payroll->id)->where('employee_id', $empC->id)->exists(), 'Emp C (Other Dept) should NOT be included');
    }
}
