<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiProjectEmployeeIdsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_retrieve_project_employee_ids_as_assigned_timekeeper()
    {
        // 1. Create a Timekeeper (Employee)
        $timekeeper = Employee::factory()->create();

        // 2. Create a Project assigned to this Timekeeper
        $project = Project::create([
            'name' => 'Test Project',
            'status' => 'active',
            'time_keeper_id' => $timekeeper->id,
            'start_date' => now(),
        ]);

        // 3. Create Employees and Assign them to the Project
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();
        $employee3 = Employee::factory()->create(); // Not assigned

        $project->employees()->attach([$employee1->id, $employee2->id]);

        // 4. Make Request
        $response = $this->getJson('/api/timekeeper/project-employee-ids?timekeeper_id=' . $timekeeper->id . '&project_id=' . $project->id);

        // 5. Assertions
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'project_id' => $project->id,
        ]);

        // Check if employee_ids array contains the correct IDs
        // JSON response might verify exact array or containment
        $this->assertTrue(in_array($employee1->id, $response->json('employee_ids')));
        $this->assertTrue(in_array($employee2->id, $response->json('employee_ids')));
        $this->assertFalse(in_array($employee3->id, $response->json('employee_ids')));
    }

    public function test_cannot_access_project_employee_ids_if_not_assigned_timekeeper()
    {
        $timekeeper1 = Employee::factory()->create();
        $timekeeper2 = Employee::factory()->create();

        $project = Project::create([
            'name' => 'Restricted Project',
            'status' => 'active',
            'time_keeper_id' => $timekeeper1->id,
        ]);

        // Timekeeper 2 tries to access Project of Timekeeper 1
        $response = $this->getJson('/api/timekeeper/project-employee-ids?timekeeper_id=' . $timekeeper2->id . '&project_id=' . $project->id);

        $response->assertStatus(403);
        $response->assertJson(['status' => 'failed']);
    }

    public function test_validates_input_parameters()
    {
        $response = $this->getJson('/api/timekeeper/project-employee-ids');
        $response->assertStatus(422);
        
        // Assert errors structure if needed, or just status is enough for basic check
    }
}
