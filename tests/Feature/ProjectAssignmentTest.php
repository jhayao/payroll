<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_assign_multiple_employees_to_project()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();

        $project = Project::create([
            'name' => 'Project Multi',
            'status' => 'active',
        ]);

        $employees = Employee::factory()->count(3)->create();
        $employeeIds = $employees->pluck('id')->toArray();

        $response = $this->actingAs($user)->post(route('projects.assign-employee', $project->id), [
            'employee_ids' => $employeeIds,
        ]);

        $response->assertSessionHas('status');
        
        foreach ($employeeIds as $id) {
            $this->assertDatabaseHas('employee_project', [
                'project_id' => $project->id,
                'employee_id' => $id,
            ]);
        }
    }
}
