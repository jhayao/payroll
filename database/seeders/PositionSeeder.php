<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('positions')->delete();

        DB::table('positions')->insert([
            ['id' => 1, 'description' => 'Office Engineer', 'daily_rate' => 900, 'hourly_rate' => 112.5, 'minutely_rate' => 1.87],
            ['id' => 5, 'description' => 'Purchasing Manager', 'daily_rate' => 900, 'hourly_rate' => 112.5, 'minutely_rate' => 1.87],
            ['id' => 6, 'description' => 'Bidding Manager', 'daily_rate' => 500, 'hourly_rate' => 62.5, 'minutely_rate' => 1.04],
            ['id' => 7, 'description' => 'Accounting', 'daily_rate' => 500, 'hourly_rate' => 62.5, 'minutely_rate' => 1.04],
            ['id' => 8, 'description' => 'HR', 'daily_rate' => 1000, 'hourly_rate' => 125, 'minutely_rate' => 2.08],
            ['id' => 10, 'description' => 'Labor', 'daily_rate' => 475, 'hourly_rate' => 59.37, 'minutely_rate' => 0.98],
            ['id' => 11, 'description' => 'Skilled Worker', 'daily_rate' => 500, 'hourly_rate' => 62.5, 'minutely_rate' => 1.04],
            ['id' => 12, 'description' => 'Foreman', 'daily_rate' => 650, 'hourly_rate' => 81.25, 'minutely_rate' => 1.35],
            ['id' => 13, 'description' => 'Leadman', 'daily_rate' => 650, 'hourly_rate' => 81.25, 'minutely_rate' => 1.35],
            ['id' => 14, 'description' => 'Time Keeper', 'daily_rate' => 500, 'hourly_rate' => 62.5, 'minutely_rate' => 1.04],
            ['id' => 15, 'description' => 'Project Incharge', 'daily_rate' => 675, 'hourly_rate' => 84.37, 'minutely_rate' => 1.4],
            ['id' => 17, 'description' => 'Chief Engineer', 'daily_rate' => 1250, 'hourly_rate' => 156.25, 'minutely_rate' => 2.6],
            ['id' => 18, 'description' => 'Driver', 'daily_rate' => 500, 'hourly_rate' => 62.5, 'minutely_rate' => 1.04],
        ]);
    }
}
