<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAssignmentRestrictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_restrict_employee_assignment_to_multiple_active_projects()
    {
        // 1. Setup
        // Create admin user and authenticate
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $employee = Employee::factory()->create();

        $projectA = Project::create([
            'name' => 'Project A Test '.uniqid(),
            'status' => 'active',
            'time_keeper_id' => null,
        ]);

        $projectB = Project::create([
            'name' => 'Project B Test '.uniqid(),
            'status' => 'active',
            'time_keeper_id' => null,
        ]);

        // 2. Assign to Project A (Should succeed)
        $response = $this->post(route('projects.assign-employee', $projectA->id), [
            'employee_ids' => [$employee->id],
        ]);

        // Assert: Redirect back with success message
        $response->assertSessionHas('status');
        $this->assertTrue($projectA->employees()->where('employees.id', $employee->id)->exists());

        // 3. Assign to Project B (Should fail)
        $response = $this->post(route('projects.assign-employee', $projectB->id), [
            'employee_ids' => [$employee->id],
        ]);

        // Assert: Redirect back with error
        $response->assertSessionHasErrors('employee_ids');
        $this->assertFalse($projectB->employees()->where('employees.id', $employee->id)->exists());

        // 4. Change Project A to completed
        $projectA->update(['status' => 'completed']);

        // 5. Assign to Project B (Should succeed)
        $response = $this->post(route('projects.assign-employee', $projectB->id), [
            'employee_ids' => [$employee->id],
        ]);

        // Assert: Success
        $response->assertSessionHas('status');
        $this->assertTrue($projectB->employees()->where('employees.id', $employee->id)->exists());

        // Cleanup
        $projectA->employees()->detach();
        $projectB->employees()->detach();
        $projectA->delete();
        $projectB->delete();
        $employee->delete();
        $admin->delete();
    }
}
