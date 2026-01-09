<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use Carbon\Carbon;

class DtrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dtr')->delete();

        $employees = Employee::all();
        $startDate = Carbon::parse('2026-01-01');
        $endDate = Carbon::parse('2026-01-09');
        
        $id = 1;
        
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
                
                // Randomly add overtime (20% chance)
                $otIn = null;
                $otOut = null;
                if (rand(1, 100) <= 20) {
                    $otIn = $pmOut->copy()->addMinutes(rand(30, 60));
                    $otOut = $otIn->copy()->addMinutes(rand(60, 180)); // 1-3 hours OT
                }
                
                DB::table('dtr')->insert([
                    'id' => $id++,
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
        
        $this->command->info('DTR records seeded for all employees from Jan 1-9, 2026');
    }
}
