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

        $positions = [
            // Accounting (Dept 2)
            ['department_id' => 2, 'description' => 'Accounting Head', 'daily_rate' => 1000],
            ['department_id' => 2, 'description' => 'Accounting 1', 'daily_rate' => 600],
            ['department_id' => 2, 'description' => 'Accounting 2', 'daily_rate' => 600],

            // HR (Dept 3)
            ['department_id' => 3, 'description' => 'Hr Head', 'daily_rate' => 1000],
            ['department_id' => 3, 'description' => 'Hr 1', 'daily_rate' => 600],

            // Bid (Dept 4)
            ['department_id' => 4, 'description' => 'Bidding Engineer', 'daily_rate' => 800],
            ['department_id' => 4, 'description' => 'Bidding Staff 1', 'daily_rate' => 600],
            ['department_id' => 4, 'description' => 'Bidding Staff 2', 'daily_rate' => 600],

            // Purchasing (Dept 5)
            ['department_id' => 5, 'description' => 'Purchasing Head', 'daily_rate' => 1000],
            ['department_id' => 5, 'description' => 'Purchasing Staff 1', 'daily_rate' => 600],
            ['department_id' => 5, 'description' => 'Purchasing Staff 2', 'daily_rate' => 600],

            // Engineering (Dept 6)
            ['department_id' => 6, 'description' => 'Chief Engineer', 'daily_rate' => 1250],
            ['department_id' => 6, 'description' => 'Residents Engineer', 'daily_rate' => 1000],
            ['department_id' => 6, 'description' => 'Office Engineer', 'daily_rate' => 900],
            ['department_id' => 6, 'description' => 'Project Incharge', 'daily_rate' => 700],
            ['department_id' => 6, 'description' => 'Panday Mason', 'daily_rate' => 550],
            ['department_id' => 6, 'description' => 'Foreman', 'daily_rate' => 650],
            ['department_id' => 6, 'description' => 'Leadman', 'daily_rate' => 600],
            ['department_id' => 6, 'description' => 'Labor', 'daily_rate' => 475],
            ['department_id' => 6, 'description' => 'Skilled Worker', 'daily_rate' => 500],
            ['department_id' => 6, 'description' => 'Timekeeper', 'daily_rate' => 500],
            ['department_id' => 6, 'description' => 'Senior Driver', 'daily_rate' => 600],
            ['department_id' => 6, 'description' => 'Mini Dump Truck Driver', 'daily_rate' => 550],
            ['department_id' => 6, 'description' => 'Dump Truck Driver', 'daily_rate' => 550],
            ['department_id' => 6, 'description' => 'Flat Bed Truck Driver', 'daily_rate' => 550],
            ['department_id' => 6, 'description' => 'Excavator Driver', 'daily_rate' => 600],
            ['department_id' => 6, 'description' => 'Bulldozer Driver', 'daily_rate' => 600],
            ['department_id' => 6, 'description' => 'Front Loader Driver', 'daily_rate' => 600],
            ['department_id' => 6, 'description' => 'Crane Driver', 'daily_rate' => 650],
            ['department_id' => 6, 'description' => 'Concrete Mixer Driver', 'daily_rate' => 550],
            ['department_id' => 6, 'description' => 'Road Roller Driver', 'daily_rate' => 600],

            // Top Management (Dept 7)
            ['department_id' => 7, 'description' => 'Admin', 'daily_rate' => 800],
        ];

        // Process data to calculate rates and timestamp
        $data = [];
        $now = now();
        foreach ($positions as $pos) {
            $hourly = $pos['daily_rate'] / 8;
            $minutely = $hourly / 60;

            $data[] = array_merge($pos, [
                'hourly_rate' => $hourly,
                'minutely_rate' => $minutely,
                // 'created_at' => $now, // If timestamps were enabled, but simple DB insert might not need it if default CURRENT_TIMESTAMP exists, or if model handles it. Seeder uses DB::table which doesn't auto-timestamp unless specified.
                // Assuming schema has defaults or nullable. Migration 2025_01_01_... creates positions.
            ]);
        }

        DB::table('positions')->insert($data);
    }
}
