<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;

class DepartmentPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'ACCOUNTING Department' => [
                'Accounting Head',
                'Accounting 1',
                'Accounting 2'
            ],
            'Human Resources Department' => [
                'Hr Head',
                'Hr 1'
            ],
            'Bid Department' => [
                'Bidding Engineer',
                'Bidding Staff 1',
                'Bidding Staff 2'
            ],
            'Purchasing Department' => [
                'Purchasing Head',
                'Purchasing Staff 1',
                'Purchasing Staff 2'
            ],
            'Engineering Department' => [
                'Chief engineer',
                'Residents Engineer',
                'Office Engineer',
                'Project Incharge',
                'Panday Mason',
                'Foreman',
                'Leadman',
                'Labor',
                'Skilled Worker',
                'Timekeeper',
                'Senior Driver',
                'Mini Dump Truck Driver',
                'Dump truck Driver',
                'Flat bed truck Driver',
                'Excavator Driver',
                'Bulldozer Driver',
                'Front Loader Driver',
                'Crane Driver',
                'Concrete Mixer Driver',
                'Road Roller Driver'
            ]
        ];

        foreach ($data as $deptName => $positions) {
            // Create or update Department
            // Assuming 'abbr' is required, we'll generate a simple one if creating new
            $abbr = strtoupper(substr($deptName, 0, 3)); 
            
            $department = Department::firstOrCreate(
                ['name' => $deptName],
                ['abbr' => $abbr]
            );

            foreach ($positions as $posName) {
                // Check if position exists
                 $position = Position::where('description', $posName)->first();

                 if ($position) {
                     // Update existing position with department
                     $position->department_id = $department->id;
                     $position->save();
                 } else {
                     // Create new position
                     // We need default rates. Setting to 0 or some default.
                     // The user didn't specify rates, so we'll defaults valid for the existing schema.
                     // Accessing existing schema requirement: daily_rate is required.
                     Position::create([
                         'description' => $posName,
                         'department_id' => $department->id,
                         'daily_rate' => 0,
                         'hourly_rate' => 0,
                         'minutely_rate' => 0
                     ]);
                 }
            }
        }
    }
}
