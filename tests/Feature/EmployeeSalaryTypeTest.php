<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EmployeeSalaryTypeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_can_create_employee_with_salary_type()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        
        // Create required Shift
        \App\Models\Shift::create([
            'name' => 'Regular Shift',
            'am_in' => '08:00',
            'am_out' => '12:00',
            'pm_in' => '13:00',
            'pm_out' => '17:00',
        ]);

        $data = [
            'lastname' => 'Doe',
            'firstname' => 'John',
            'middlename' => 'Allan',
            'suffix' => '', // Add suffix
            'mobile_no' => '09123456789',
            'purok' => 'Purok 1',
            'barangay' => 'Barangay 1',
            'city' => 'City 1',
            'sex' => 'Male',
            'salary_type' => 'weekly',
            'photo' => UploadedFile::fake()->image('photo.jpg'),
            'photo2' => UploadedFile::fake()->image('photo2.jpg'),
            'photo3' => UploadedFile::fake()->image('photo3.jpg'),
        ];

        // Ensure acting as user for permissions
        $response = $this->actingAs($user)->post(route('employees.save'), $data);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('employees', [
            'lastname' => 'Doe',
            'salary_type' => 'weekly',
        ]);
    }

    public function test_can_update_employee_salary_type()
    {
        $user = User::factory()->create();
        $user->role = 'admin';

        $employee = Employee::factory()->create([
            'salary_type' => 'weekly',
            'mobile_no' => '09123456789',
        ]);

        $response = $this->actingAs($user)->post(route('employees.update', $employee->id), [
            'lastname' => $employee->lastname,
            'firstname' => $employee->firstname,
            'middlename' => $employee->middlename ?? '',
            'suffix' => $employee->suffix ?? '',
            'mobile_no' => $employee->mobile_no,
            'salary_type' => 'semi_monthly',
            'sex' => $employee->sex,
            // 'department' => $employee->department_id,
            // 'position' => $employee->position_id,
            'purok' => $employee->purok,
            'barangay' => $employee->barangay,
            'city' => $employee->city,
        ]);

        $response->assertSessionHas('status'); // or whatever success message/redirect is used.
        // The update usually redirects back.
        
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'salary_type' => 'semi_monthly',
        ]);
    }
}
