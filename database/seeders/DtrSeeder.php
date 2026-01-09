<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DtrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dtr')->delete();

        DB::table('dtr')->insert([
            ['id' => 11, 'employee_id' => 1761536897, 'log_date' => '2025-12-03', 'am_in' => '2025-12-03 09:10:00', 'am_out' => '2025-12-03 12:20:00', 'pm_in' => '2025-12-03 13:46:00', 'pm_out' => '2025-12-03 17:21:00', 'ot_in' => null, 'ot_out' => null],
            ['id' => 12, 'employee_id' => 1761536897, 'log_date' => '2025-12-04', 'am_in' => '2025-12-04 10:01:00', 'am_out' => '2025-12-04 12:03:00', 'pm_in' => '2025-12-04 13:04:00', 'pm_out' => '2025-12-04 17:04:00', 'ot_in' => null, 'ot_out' => null],
            ['id' => 13, 'employee_id' => 1761536897, 'log_date' => '2025-12-05', 'am_in' => '2025-12-05 08:04:00', 'am_out' => '2025-12-05 11:05:00', 'pm_in' => '2025-12-05 12:16:00', 'pm_out' => '2025-12-05 16:56:00', 'ot_in' => null, 'ot_out' => null],
            ['id' => 14, 'employee_id' => 1761536897, 'log_date' => '2025-12-06', 'am_in' => '2025-12-06 08:49:00', 'am_out' => '2025-12-06 11:49:00', 'pm_in' => '2025-12-06 13:50:00', 'pm_out' => '2025-12-06 17:51:08', 'ot_in' => '2025-12-06 18:00:00', 'ot_out' => '2025-12-06 18:54:00'],
            ['id' => 15, 'employee_id' => 1761536897, 'log_date' => '2025-12-08', 'am_in' => '2025-12-08 07:49:00', 'am_out' => '2025-12-08 12:00:00', 'pm_in' => '2025-12-08 13:00:00', 'pm_out' => '2025-12-08 16:51:16', 'ot_in' => '2025-12-08 17:20:00', 'ot_out' => '2025-12-08 18:53:00'],
            ['id' => 16, 'employee_id' => 1761536897, 'log_date' => '2025-12-09', 'am_in' => '2025-12-09 06:49:00', 'am_out' => '2025-12-09 11:50:00', 'pm_in' => '2025-12-09 12:50:00', 'pm_out' => '2025-12-09 17:01:28', 'ot_in' => '2025-12-09 17:30:00', 'ot_out' => '2025-12-09 18:52:00'],
            ['id' => 22, 'employee_id' => 1761536897, 'log_date' => '2025-12-20', 'am_in' => '2025-12-20 14:19:00', 'am_out' => null, 'pm_in' => null, 'pm_out' => null, 'ot_in' => null, 'ot_out' => null],
            ['id' => 23, 'employee_id' => 1766291621, 'log_date' => '2025-12-21', 'am_in' => null, 'am_out' => null, 'pm_in' => '2025-12-21 12:52:00', 'pm_out' => '2025-12-21 17:17:00', 'ot_in' => null, 'ot_out' => null],
            ['id' => 24, 'employee_id' => 1766294697, 'log_date' => '2025-12-21', 'am_in' => null, 'am_out' => null, 'pm_in' => '2025-12-21 13:42:00', 'pm_out' => '2025-12-21 17:17:00', 'ot_in' => null, 'ot_out' => null],
            ['id' => 25, 'employee_id' => 1761536897, 'log_date' => '2025-12-21', 'am_in' => null, 'am_out' => null, 'pm_in' => null, 'pm_out' => null, 'ot_in' => '2025-12-21 18:45:00', 'ot_out' => null],
            ['id' => 26, 'employee_id' => 1766291621, 'log_date' => '2025-12-22', 'am_in' => null, 'am_out' => null, 'pm_in' => '2025-12-22 13:36:00', 'pm_out' => null, 'ot_in' => null, 'ot_out' => null],
            ['id' => 27, 'employee_id' => 1766291621, 'log_date' => '2026-01-06', 'am_in' => null, 'am_out' => null, 'pm_in' => null, 'pm_out' => null, 'ot_in' => '2026-01-06 19:23:00', 'ot_out' => '2026-01-06 21:05:00'],
        ]);
    }
}
