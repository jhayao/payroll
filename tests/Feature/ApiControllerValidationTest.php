<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiControllerValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_make_log_validation()
    {
        $response = $this->postJson('api/dtr/make-log', [
            'employee_id' => 99999, // Non-existent
            'mark' => 'am_in',
            'date_log' => now()->toDateString(),
            'time_log' => now()->toTimeString()
        ]);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['employee_id']);
    }

    public function test_view_dtr_validation()
    {
        // ViewDTR is POST to api/dtr/view
        $response = $this->postJson('api/dtr/view', [
            'employee_id' => 99999,
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-31'
        ]);
        
        // Since it returns a View, validate response might be redirect if not ajax? 
        // But postJson sets ajax headers.
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['employee_id']);
    }

    public function test_get_timekeeper_projects_validation()
    {
        // GET request
        $response = $this->getJson('api/timekeeper/projects?timekeeper_id=99999');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['timekeeper_id']);
    }

    public function test_get_timekeeper_employees_validation()
    {
        $employee = Employee::factory()->create();
        
        // Invalid Timekeeper (GET)
        $response = $this->getJson('api/timekeeper/employees?timekeeper_id=99999');
        $response->assertStatus(422)->assertJsonValidationErrors(['timekeeper_id']);

        // Invalid Project (GET)
        $response = $this->getJson("api/timekeeper/employees?timekeeper_id={$employee->id}&project_id=99999");
        $response->assertStatus(422)->assertJsonValidationErrors(['project_id']);
    }

    public function test_get_timekeeper_attendance_validation()
    {
        $employee = Employee::factory()->create();

        // GET request
        $url = "api/timekeeper/attendance?timekeeper_id={$employee->id}&project_id=99999&date_from=2024-01-01&date_to=2024-01-01";
        $response = $this->getJson($url);
        $response->assertStatus(422)->assertJsonValidationErrors(['project_id']);
    }
}
