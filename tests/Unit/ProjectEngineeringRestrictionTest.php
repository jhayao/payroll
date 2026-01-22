<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectEngineeringRestrictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_engineering_employees_can_be_assigned()
    {
        // 1. Setup
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $engDept = Department::create(['name' => 'Engineering Department', 'abbr' => 'Engr.']);
        $otherDept = Department::create(['name' => 'HR Department', 'abbr' => 'HR']);

        $engEmployee = Employee::factory()->create([
            'department_id' => $engDept->id, 
            'firstname' => 'Eng',
            'lastname' => 'User'
        ]);

        $hrEmployee = Employee::factory()->create([
            'department_id' => $otherDept->id,
            'firstname' => 'HR',
            'lastname' => 'User'
        ]);

        $project = Project::create([
            'name' => 'Test Project',
            'status' => 'active'
        ]);

        // 2. Assign Engineering Employee (Success)
        $response = $this->post(route('projects.assign-employee', $project->id), [
            'employee_ids' => [$engEmployee->id]
        ]);
        $response->assertSessionHas('status');
        $this->assertTrue($project->employees()->where('employees.id', $engEmployee->id)->exists());

        // 3. Assign HR Employee (Fail)
        $response = $this->post(route('projects.assign-employee', $project->id), [
            'employee_ids' => [$hrEmployee->id]
        ]);
        $response->assertSessionHasErrors('employee_ids');
        $this->assertFalse($project->employees()->where('employees.id', $hrEmployee->id)->exists());
    }
}
