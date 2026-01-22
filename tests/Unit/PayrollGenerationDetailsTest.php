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

class PayrollGenerationDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_generation_info_attribute()
    {
        // 1. Setup
        $dept = Department::create(['name' => 'IT', 'abbr' => 'IT']);
        $project = Project::create(['name' => 'Project Alpha', 'status' => 'active']);
        $employee = Employee::factory()->create(['department_id' => $dept->id, 'firstname' => 'John', 'lastname' => 'Doe', 'custom_daily_rate' => 500, 'salary_type' => 'weekly']);

        // 2. Test "All" (Department)
        $payrollAll = Payroll::create([
            'department_id' => $dept->id,
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-15',
            'type' => 'all'
        ]);
        $this->assertEquals('Department - IT', $payrollAll->generation_info);

        // 3. Test "Project"
        $payrollProject = Payroll::create([
            'department_id' => $dept->id,
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-15',
            'type' => 'project',
            'project_id' => $project->id
        ]);
        $this->assertEquals('Project - Project Alpha', $payrollProject->generation_info);

        // 4. Test "Individual"
        $payrollIndividual = Payroll::create([
            'department_id' => $dept->id,
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-15',
            'type' => 'individual',
            'employee_id' => $employee->id
        ]);
        $this->assertEquals("Individual - " . $employee->fullname, $payrollIndividual->generation_info);
    }
}
