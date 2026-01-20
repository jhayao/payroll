<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class UpdateEmployeeIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-employee-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing employee IDs to 6-digit shorter format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting employee ID update...');

        $employees = Employee::all();
        $bar = $this->output->createProgressBar(count($employees));

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($employees as $employee) {
            $oldId = $employee->id;
            
            // Skip if already 6 digits (just in case)
            if (strlen((string)$oldId) <= 6) {
                $bar->advance();
                continue;
            }

            // Generate new 6-digit ID
            do {
                $newId = mt_rand(100000, 999999);
            } while (DB::table('employees')->where('id', $newId)->exists());

            try {
                // Update primary record
                DB::table('employees')->where('id', $oldId)->update(['id' => $newId]);

                // Update related tables
                DB::table('dtr')->where('employee_id', $oldId)->update(['employee_id' => $newId]);
                DB::table('employee_project')->where('employee_id', $oldId)->update(['employee_id' => $newId]);
                DB::table('employee_shifts')->where('employee_id', $oldId)->update(['employee_id' => $newId]);
                DB::table('payroll_items')->where('employee_id', $oldId)->update(['employee_id' => $newId]);
                // Pivot tables often use direct table names, checking model relations...
                // Assuming 'allowance_employee' and 'deduction_employee' based on previous context 
                // but usually pivot tables are singular alphabetical e.g., 'allowance_employee' or 'deduction_employee'
                // Re-verifying pivot names from Employee.php relations:
                // allowances(): belongsToMany(Allowance::class, 'allowance_employee')
                DB::table('allowance_employee')->where('employee_id', $oldId)->update(['employee_id' => $newId]);
                DB::table('deduction_employee')->where('employee_id', $oldId)->update(['employee_id' => $newId]);
                
                // Managed projects
                DB::table('projects')->where('time_keeper_id', $oldId)->update(['time_keeper_id' => $newId]);

            } catch (\Exception $e) {
                $this->error("Failed to update Employee $oldId: " . $e->getMessage());
            }

            $bar->advance();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $bar->finish();
        $this->newLine();
        $this->info('Employee IDs updated successfully.');
    }
}
