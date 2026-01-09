<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('allowances')->delete();

        DB::table('allowances')->insert([
            ['id' => 1, 'description' => 'Food'],
            ['id' => 2, 'description' => 'Travel'],
            ['id' => 5, 'description' => 'Bonus'],
        ]);
    }
}
