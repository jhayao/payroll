<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use Carbon\Carbon;

class DecemberDtrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We don't delete existing records here to allow multiple seeders to coexist
        // or the user might want both Jan and Dec.
        
        $employees = Employee::all();
        $startDate = Carbon::parse('2025-12-01');
        $endDate = Carbon::parse('2025-12-31');
        
        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Skip Sundays
                if ($currentDate->dayOfWeek === Carbon::SUNDAY) {
                    $currentDate->addDay();
                    continue;
                }
                
                $dateString = $currentDate->toDateString();
                
                // Get the employee's shift times
                $shift = $employee->currentShift()?->shift;
                
                if (!$shift) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Generate realistic time variations (±5-15 minutes)
                $amInVariation = rand(-5, 15);
                $amOutVariation = rand(-5, 5);
                $pmInVariation = rand(-5, 10);
                $pmOutVariation = rand(-10, 5);
                
                // Parse shift times and add variations
                $amIn = Carbon::parse($dateString . ' ' . $shift->am_in)->addMinutes($amInVariation);
                $amOut = Carbon::parse($dateString . ' ' . $shift->am_out)->addMinutes($amOutVariation);
                $pmIn = Carbon::parse($dateString . ' ' . $shift->pm_in)->addMinutes($pmInVariation);
                $pmOut = Carbon::parse($dateString . ' ' . $shift->pm_out)->addMinutes($pmOutVariation);
                
                // Randomly add overtime (15% chance for Dec)
                $otIn = null;
                $otOut = null;
                if (rand(1, 100) <= 15) {
                    $otIn = $pmOut->copy()->addMinutes(rand(30, 60));
                    $otOut = $otIn->copy()->addMinutes(rand(60, 150)); // 1-2.5 hours OT
                }
                
                DB::table('dtr')->insert([
                    'employee_id' => $employee->id,
                    'log_date' => $dateString,
                    'am_in' => $amIn->format('Y-m-d H:i:s'),
                    'am_out' => $amOut->format('Y-m-d H:i:s'),
                    'pm_in' => $pmIn->format('Y-m-d H:i:s'),
                    'pm_out' => $pmOut->format('Y-m-d H:i:s'),
                    'ot_in' => $otIn?->format('Y-m-d H:i:s'),
                    'ot_out' => $otOut?->format('Y-m-d H:i:s'),
                ]);
                
                $currentDate->addDay();
            }
        }
        
        $this->command->info('DTR records seeded for all employees for December 2025');
    }
}
