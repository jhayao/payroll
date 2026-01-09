<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shifts')->delete();

        DB::table('shifts')->insert([
            [
                'id' => 1,
                'name' => 'Regular Shift',
                'am_in' => '08:00',
                'am_out' => '12:00',
                'pm_in' => '13:00',
                'pm_out' => '17:00',
                'in_out_interval' => 60,
                'out_in_interval' => 10,
            ],
            [
                'id' => 5,
                'name' => 'Holiday shifts',
                'am_in' => '08:00',
                'am_out' => '12:00',
                'pm_in' => '01:00',
                'pm_out' => '05:00',
                'in_out_interval' => 0,
                'out_in_interval' => 0,
            ],
        ]);
    }
}
