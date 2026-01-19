<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTimekeeperTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_assign_unique_timekeeper_to_project()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();

        $employee = Employee::factory()->create();
        
        $response = $this->actingAs($user)->post(route('projects.save'), [
            'name' => 'Project A',
            'status' => 'active',
            'time_keeper_id' => $employee->id,
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('projects', ['time_keeper_id' => $employee->id]);
    }

    public function test_cannot_assign_already_assigned_timekeeper_to_new_project()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();

        $employee = Employee::factory()->create();
        Project::create([
            'name' => 'Existing Project',
            'status' => 'active',
            'time_keeper_id' => $employee->id,
        ]);

        $response = $this->actingAs($user)->post(route('projects.save'), [
            'name' => 'New Project',
            'status' => 'active',
            'time_keeper_id' => $employee->id,
        ]);

        $response->assertSessionHasErrors(['time_keeper_id']);
    }

     public function test_cannot_update_project_with_already_assigned_timekeeper()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();

        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create(); // Assigned to Project B
        
        $projectA = Project::create([
            'name' => 'Project A',
            'status' => 'active',
            'time_keeper_id' => $employee1->id,
        ]);

        $projectB = Project::create([
            'name' => 'Project B',
            'status' => 'active',
            'time_keeper_id' => $employee2->id,
        ]);

        // Try to assign Employee 2 (from Project B) to Project A
        $response = $this->actingAs($user)->post(route('projects.update', $projectA->id), [
            'name' => 'Project A Updated',
            'status' => 'active',
            'time_keeper_id' => $employee2->id,
        ]);

        $response->assertSessionHasErrors(['time_keeper_id']);
    }
}
