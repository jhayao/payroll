<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees')->delete();
        DB::table('employee_shifts')->delete();
        DB::table('employee_deductions')->delete();

        // Employees
        DB::table('employees')->insert([
            [
                'id' => 1761536897,
                'lastname' => 'Doe',
                'firstname' => 'Jane',
                'middlename' => 'M.',
                'suffix' => '',
                'sex' => 'Female',
                'address' => 'Bangkok Thailand',
                'mobile_no' => '09077896547',
                'position_id' => 15,
                'department_id' => 5,
                'email' => 'janedoe@gmail.com',
                'password' => '$2y$12$9bwwtkcdc5.SGokpMlxFxOY.dyaudGZIzK9zEWiNbBxVhf4aaNqKa',
                'remember_token' => 'Lw8fV2aSmCUWuWiJ4lYhAMr82fcnrXCDTP3by5pIHEylv4DZHLhEqONBHcS4',
                'photo_2x2' => '/images/uploads/2x2/1761536897.jpg',
                'photo_lg' => '/images/uploads/1761536897/photo.jpg',
                'photo_lg2' => '/images/uploads/1761536897/photo3.jpg',
                'photo_lg3' => '/images/uploads/1761536897/photo3.jpg',
            ],
            [
                'id' => 1766291621,
                'lastname' => 'Dimasuhid',
                'firstname' => 'Jhap Jessel',
                'middlename' => 'N.',
                'suffix' => '',
                'sex' => 'Female',
                'address' => 'Tangub City',
                'mobile_no' => '09051532389',
                'position_id' => 1,
                'department_id' => 6,
                'email' => 'jessel@gmail.com',
                'password' => '$2y$12$Z52ReDQNYKBCqzK6LdTlbeNC1fE/R5E0eQCtAoMIX4UU7Ad6AUx92',
                'remember_token' => null,
                'photo_2x2' => '/images/uploads/2x2/1766291621.jpg',
                'photo_lg' => '/images/uploads/1766291621/photo.jpg',
                'photo_lg2' => '/images/uploads/1766291621/photo3.jpg',
                'photo_lg3' => '/images/uploads/1766291621/photo3.jpg',
            ],
            [
                'id' => 1766294697,
                'lastname' => 'Fernandez',
                'firstname' => 'Sarah Jean',
                'middlename' => 'Campilan',
                'suffix' => '',
                'sex' => 'Female',
                'address' => 'Bintana, Tangub City',
                'mobile_no' => '09567549198',
                'position_id' => 6,
                'department_id' => 3,
                'email' => 'sarahjean@gmail.com',
                'password' => '$2y$12$yJblZeI4dvcv9nv.D3/tHOp0XR7Vf9iF0fNneu4dPj5yG79eoaUW2',
                'remember_token' => null,
                'photo_2x2' => '/images/uploads/2x2/1766294697.jpg',
                'photo_lg' => '/images/uploads/1766294697/photo.jpg',
                'photo_lg2' => '/images/uploads/1766294697/photo3.jpg',
                'photo_lg3' => '/images/uploads/1766294697/photo3.jpg',
            ],
            [
                'id' => 1767706548,
                'lastname' => 'Duhaylungsod',
                'firstname' => 'Welmar',
                'middlename' => 'P.',
                'suffix' => '',
                'sex' => 'Male',
                'address' => 'Capalaran, Tangub City',
                'mobile_no' => '09754660911',
                'position_id' => 18,
                'department_id' => 6,
                'email' => 'welmarduhaylungsod@gmail.com',
                'password' => '$2y$12$vzo1KQJbWi5r9Ug9ac.IHOIzLn0dRXsYf0sogdbBvqWUkkF7WotW2',
                'remember_token' => null,
                'photo_2x2' => '/images/uploads/2x2/1767706548.jpg',
                'photo_lg' => '/images/uploads/1767706548/photo.jpg',
                'photo_lg2' => '/images/uploads/1767706548/photo3.jpg',
                'photo_lg3' => '/images/uploads/1767706548/photo3.jpg',
            ],
        ]);

        // Employee Shifts
        DB::table('employee_shifts')->insert([
            ['id' => 1, 'employee_id' => 1761536897, 'shift_id' => 5, 'remarks' => 'active'],
            ['id' => 2, 'employee_id' => 1766291621, 'shift_id' => 1, 'remarks' => 'active'],
            ['id' => 3, 'employee_id' => 1766294697, 'shift_id' => 1, 'remarks' => 'active'],
            ['id' => 4, 'employee_id' => 1767706548, 'shift_id' => 1, 'remarks' => 'active'],
        ]);

        // Employee Deductions
        DB::table('employee_deductions')->insert([
            ['id' => 3, 'payroll_item_id' => 10, 'description' => 'Tardiness 7h, 12m', 'amount' => 809.21133333333],
            ['id' => 4, 'payroll_item_id' => 10, 'description' => 'Cash Advance', 'amount' => 3000],
        ]);
    }
}
