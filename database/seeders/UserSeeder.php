<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            [
                'id' => 2,
                'name' => 'Hr',
                'role' => 'hr',
                'position' => 'HR Head',
                'email' => 'hr@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$12$hfJ1CrNH5uFmNWFHTVD5PuKvqY5yVEjyzQppRrNVcR.7T3Fa2mVRe',
                'remember_token' => null,
                'created_at' => '2025-12-14 07:44:56',
                'updated_at' => '2025-12-18 06:04:58',
            ],
            [
                'id' => 3,
                'name' => 'Admin',
                'role' => 'admin',
                'position' => 'System Administrator',
                'email' => 'admin@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$12$Y44/ts3J3ZcQQXLaTcEHe.7SFXADyvATcikgPltc9ffJuRu64pqb.',
                'remember_token' => '74B8GTmMiNSEVFE2Rzmaun8WUayZbsJTIyct9rg22B0GYpf6oxyFo3DRbrnr',
                'created_at' => '2025-12-18 06:05:09',
                'updated_at' => '2025-12-19 10:51:52',
            ],
            [
                'id' => 4,
                'name' => 'Accounting',
                'role' => 'accounting',
                'position' => 'Accounting Manager',
                'email' => 'accounting@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$12$m.NDhiqb1L6qSoSd5lnHC.14iTOiQ.Evo/vakHIpbLzOHcZ2ZWwVq',
                'remember_token' => null,
                'created_at' => '2025-12-18 06:35:12',
                'updated_at' => '2025-12-18 06:36:08',
            ],
            [
                'id' => 5,
                'name' => 'Timekeeper',
                'role' => 'timekeeper',
                'position' => 'Timekeeper',
                'email' => 'timekeeper@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$12$sITqwQgldpIo6M3hzFlKJOU.9nzxFiQa50tM6nh1Ch2l97X2.f2dq',
                'remember_token' => null,
                'created_at' => '2025-12-19 13:49:13',
                'updated_at' => '2025-12-19 14:17:53',
            ],
        ]);
    }
}
