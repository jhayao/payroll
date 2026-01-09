<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->delete();

        DB::table('departments')->insert([
            ['id' => 2, 'name' => 'Accounting Department', 'abbr' => 'Acct. Dept'],
            ['id' => 3, 'name' => 'Human Resource Department', 'abbr' => 'HRD'],
            ['id' => 4, 'name' => 'Bid Department', 'abbr' => 'BD'],
            ['id' => 5, 'name' => 'Purchasing Department', 'abbr' => 'Pur. Dept'],
            ['id' => 6, 'name' => 'Engineering Department', 'abbr' => 'Engr.'],
            ['id' => 7, 'name' => 'Top Management', 'abbr' => 'Admin'],
        ]);
    }
}
