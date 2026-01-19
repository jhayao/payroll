<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lastname' => fake()->lastName(),
            'firstname' => fake()->firstName(),
            'middlename' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'position_id' => 1, // Assuming position exists or is nullable, but based on schema it likely exists. logic might need Position factory too.
            'department_id' => 1,
            'sex' => 'Male',
            'mobile_no' => '09123456789',
            'address' => fake()->address(),
            'employee_id' => fake()->unique()->numerify('EMP-#####'),
            'custom_daily_rate' => 500,
        ];
    }
}
