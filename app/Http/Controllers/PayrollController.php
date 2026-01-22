<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDeduction;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Position;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('items')
            ->orderBy('id', 'desc')
            ->get();

        return view('payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $departments = Department::pluck('name', 'id');
        // Fetch employees with department_id for JS filtering
        $employees = Employee::select('id', 'department_id', 'lastname', 'firstname', 'middlename', 'salary_type')->orderBy('lastname')->get()->map(function ($e) {
            $e->fullname = $e->lastname.', '.$e->firstname;

            return $e;
        });

        // Fetch active projects
        $projects = \App\Models\Project::where('status', 'active')->pluck('name', 'id');

        return view('payroll.create', compact('departments', 'employees', 'projects'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'generation_type' => ['required', 'in:all,individual,project'],
            'employee_id' => ['nullable', 'required_if:generation_type,individual', 'exists:employees,id'],
            'project_id' => ['nullable', 'required_if:generation_type,project', 'exists:projects,id'],
            'department' => ['required', 'exists:departments,id'],
            'salary_type' => ['nullable', 'in:weekly,semi_monthly'],
        ], [
            'date_from.required' => 'This is required.',
            'date_from.date' => 'This must be a valid date.',
            'date_to.required' => 'This is required.',
            'date_to.date' => 'This must be a valid date.',
            'date_to.after_or_equal' => 'This must after or equal to date from.',
            'employee_id.required_if' => 'Please select an employee for individual generation.',
            'project_id.required_if' => 'Please select a project.',
        ]);

        $payroll = Payroll::where('department_id', $request->department)
            ->whereDate('date_from', $request->date_from)
            ->whereDate('date_to', $request->date_to)
            ->where('salary_type', $request->salary_type) // Include salary type check
            ->first();

        if (! $payroll) {
            $payroll = Payroll::create([
                'department_id' => $request->department,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'salary_type' => $request->salary_type,
                'type' => $request->generation_type,
                'project_id' => $request->project_id,
                'employee_id' => $request->employee_id,
            ]);
        }

        if ($request->generation_type === 'all') {
            // Logic continued below...
        }

        $allAllowances = Allowance::with(['positions', 'employees'])->get();
        $allDeductions = Deduction::with(['positions', 'employees'])->get();
        // Pre-fetch commonly used data
        $holidays = Holiday::whereBetween('date', [$request->date_from, $request->date_to])->get()->keyBy('date');
        $holidayShifts = Shift::where('is_holiday', true)->get()->keyBy('name');

        $employeesToProcess = [];

        if ($request->generation_type === 'individual') {
            $employee = Employee::find($request->employee_id);
            if ($employee->department_id != $request->department) {
                throw ValidationException::withMessages(['employee_id' => 'Employee does not belong to the selected department.']);
            }
            $employeesToProcess[] = $employee;
        } elseif ($request->generation_type === 'project') {
            $project = \App\Models\Project::findOrFail($request->project_id);
            // Get active employees in this project belonging to the selected department
            $query = $project->employees()
                ->where('department_id', $request->department);

            if ($request->salary_type) {
                $query->where('salary_type', $request->salary_type);
            }
            $employeesToProcess = $query->get();

        } else {
            $query = Employee::where('department_id', $request->department);

            if ($request->salary_type) {
                $query->where('salary_type', $request->salary_type);
            }

            $employeesToProcess = $query->get();
        }

        $itemCount = 0;

        foreach ($employeesToProcess as $e) {
            // Check if item exists
            if (PayrollItem::where('payroll_id', $payroll->id)->where('employee_id', $e->id)->exists()) {
                continue;
            }

            // Splitting Logic for Semi-Monthly
            $start = Carbon::parse($request->date_from);
            $end = Carbon::parse($request->date_to);

            // Check if full month (Start of Month to End of Month)
            $isFullMonth = $start->copy()->startOfMonth()->equalTo($start) && $end->copy()->endOfMonth()->equalTo($end);

            if ($e->salary_type === 'semi_monthly' && $isFullMonth) {
                // Split 1: 1st to 15th
                $firstHalfEnd = $start->copy()->day(15);
                $this->calculatePayrollItem($payroll, $e, $start->format('Y-m-d'), $firstHalfEnd->format('Y-m-d'), $allAllowances, $allDeductions, $holidays, $holidayShifts);
                $itemCount++;

                // Split 2: 16th to End
                $secondHalfStart = $start->copy()->day(16);
                $this->calculatePayrollItem($payroll, $e, $secondHalfStart->format('Y-m-d'), $end->format('Y-m-d'), $allAllowances, $allDeductions, $holidays, $holidayShifts);
                $itemCount++;
            } else {
                // Normal processing
                $this->calculatePayrollItem($payroll, $e, $request->date_from, $request->date_to, $allAllowances, $allDeductions, $holidays, $holidayShifts);
                $itemCount++;
            }
        }

        if ($itemCount == 0 && count($employeesToProcess) > 0) {
            // Maybe duplicates prevented any creation
            return redirect()->route('payroll.view', $payroll->id)->with('status', 'Payroll accessed. No new items created (already existing).');
        }

        return redirect()->route('payroll.view', $payroll->id)->with('status', 'Payroll updated successfully.');
    }

    private function calculatePayrollItem($payroll, $e, $from, $to, $allAllowances, $allDeductions, $holidays, $holidayShifts)
    {
        $numDays = $e->numberOfDutyDays($from, $to);
        $overtime = $e->overtime($from, $to);
        $tardiness = $e->tardiness($from, $to);
        $dailyRate = $e->daily_rate;
        $overtime_pay = $overtime * $e->minutely_rate;

        // Calculate Holiday Pay
        $logs = $e->dtrRange($from, $to);
        $holidayPay = 0;

        foreach ($logs as $log) {
            if ($holidays->has($log->log_date)) {
                $holiday = $holidays[$log->log_date];
                // Find corresponding shift for the holiday type
                $shiftRate = 100;
                if ($holidayShifts->has($holiday->type)) {
                    $shiftRate = $holidayShifts[$holiday->type]->rate_percentage;
                }

                if ($shiftRate > 100) {
                    // Extra pay = DailyRate * ((Rate% - 100) / 100)
                    $extraPercentage = ($shiftRate - 100) / 100;
                    $holidayPay += $dailyRate * $extraPercentage;
                }
            }
        }

        $gross = ($numDays * $dailyRate) + $overtime_pay + $holidayPay;

        $tHour = floor($tardiness['grandTotal'] / 60);
        $tMins = $tardiness['grandTotal'] % 60;
        $tAmount = $tardiness['grandTotal'] * $e->minutely_rate;

        $payrollItem = PayrollItem::create([
            'payroll_id' => $payroll->id,
            'employee_id' => $e->id,
            'date_from' => $from,
            'date_to' => $to,
            'num_of_days' => $numDays,
            'daily_rate' => $e->daily_rate,
            'overtime' => $overtime,
            'overtime_pay' => $overtime_pay,
            'undertime_minutes' => $tardiness['grandTotal'],
            'undertime_amount' => $tAmount,
            'gross_pay' => $gross,
            'net_pay' => $gross - $tAmount,
        ]);

        // Auto-add Allowances
        foreach ($allAllowances as $allowance) {
            // Check Schedule
            $payrollDate = \Carbon\Carbon::parse($to);
            if ($allowance->schedule === 'specific_month') {
                if ($allowance->target_month != $payrollDate->month) {
                    continue;
                }
                if ($allowance->target_year && $allowance->target_year != $payrollDate->year) {
                    continue;
                }
            }

            $amount = 0;
            $percentage = 0;
            $shouldApply = false;

            if ($allowance->scope === 'all') {
                $shouldApply = true;
                if ($allowance->type === 'fixed') {
                    $amount = $allowance->amount;
                } elseif ($allowance->type === 'percentage') {
                    $percentage = $allowance->percentage;
                }
            } elseif ($allowance->scope === 'position') {
                $pivot = $allowance->positions->where('id', $e->position_id)->first();
                if ($pivot) {
                    $shouldApply = true;
                    if ($allowance->type === 'fixed') {
                        $amount = $pivot->pivot->amount;
                    } elseif ($allowance->type === 'percentage') {
                        $percentage = $pivot->pivot->percentage;
                    }
                }
            } elseif ($allowance->scope === 'employee') {
                $pivot = $allowance->employees->where('id', $e->id)->first();
                if ($pivot) {
                    $shouldApply = true;
                    if ($allowance->type === 'fixed') {
                        $amount = $pivot->pivot->amount;
                    } elseif ($allowance->type === 'percentage') {
                        $percentage = $pivot->pivot->percentage;
                    }
                }
            }

            if ($shouldApply) {
                // Calculate Amount if Percentage
                if ($allowance->type === 'percentage' && $percentage > 0) {
                    $basicPay = $numDays * $dailyRate;
                    $amount = $basicPay * ($percentage / 100);
                }

                if ($amount > 0) {
                    EmployeeAllowance::create([
                        'payroll_item_id' => $payrollItem->id,
                        'description' => $allowance->description,
                        'amount' => $amount,
                    ]);
                }
            }
        }

        // Auto-add Deductions
        foreach ($allDeductions as $deduction) {
            // Check Schedule
            $payrollDate = \Carbon\Carbon::parse($to);
            if ($deduction->schedule === 'specific_month') {
                if ($deduction->target_month != $payrollDate->month) {
                    continue;
                }
                if ($deduction->target_year && $deduction->target_year != $payrollDate->year) {
                    continue;
                }
            }

            $amount = 0;
            $percentage = 0;
            $shouldApply = false;

            if ($deduction->scope === 'all') {
                $shouldApply = true;
                if ($deduction->type === 'fixed') {
                    $amount = $deduction->amount;
                } elseif ($deduction->type === 'percentage') {
                    $percentage = $deduction->percentage;
                }
            } elseif ($deduction->scope === 'position') {
                $pivot = $deduction->positions->where('id', $e->position_id)->first();
                if ($pivot) {
                    $shouldApply = true;
                    if ($deduction->type === 'fixed') {
                        $amount = $pivot->pivot->amount;
                    } elseif ($deduction->type === 'percentage') {
                        $percentage = $pivot->pivot->percentage;
                    }
                }
            } elseif ($deduction->scope === 'employee') {
                $pivot = $deduction->employees->where('id', $e->id)->first();
                if ($pivot) {
                    $shouldApply = true;
                    if ($deduction->type === 'fixed') {
                        $amount = $pivot->pivot->amount;
                    } elseif ($deduction->type === 'percentage') {
                        $percentage = $pivot->pivot->percentage;
                    }
                }
            }

            if ($shouldApply) {
                // Calculate Amount if Percentage
                if ($deduction->type === 'percentage' && $percentage > 0) {
                    $basicPay = $numDays * $dailyRate;
                    $amount = $basicPay * ($percentage / 100);
                }

                if ($amount > 0) {
                    EmployeeDeduction::create([
                        'payroll_item_id' => $payrollItem->id,
                        'description' => $deduction->description,
                        'amount' => $amount,
                    ]);
                }
            }
        }

        EmployeeDeduction::create([
            'payroll_item_id' => $payrollItem->id,
            'description' => "Tardiness {$tHour}h, {$tMins}m",
            'amount' => $tAmount,
        ]);

        if ($holidayPay > 0) {
            EmployeeAllowance::create([
                'payroll_item_id' => $payrollItem->id,
                'description' => 'Holiday Pay',
                'amount' => $holidayPay,
            ]);
        }

        // Recalculate and update Net Pay in DB to match dynamic calculation
        $payrollItem->update([
            'net_pay' => $payrollItem->netPay(),
        ]);
    }

    public function view($id)
    {
        $payroll = Payroll::find($id);

        return view('payroll.view', compact('payroll'));
    }

    public function itemView($id, $item_id)
    {
        $payroll = Payroll::find($id);
        $payrollItem = PayrollItem::find($item_id);
        $deductions = Deduction::pluck('description', 'description');
        $deductions->put('Other', 'Other');
        $allowances = Allowance::pluck('description', 'description');
        $allowances->put('Other', 'Other');

        return view('payroll.item', compact('payroll', 'payrollItem', 'deductions', 'allowances'));
    }

    // Employee Allowance & Deductions

    public function saveEmployeeAllowance(Request $request)
    {
        $request->validate([
            'a_description' => ['required'],
            'a_other' => ['required_if:a_description,Other'],
            'a_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'min:1'],
        ], [
            'a_other.required_if' => 'Please specify the other allowance.',
            'a_amount.regex' => 'Please specify a valid amount.',
            'a_amount.min' => 'Amount at least 1.',
        ]);

        $description = $request->a_description === 'Other' ? $request->a_other : $request->a_description;

        $empAllowance = EmployeeAllowance::where('payroll_item_id', $request->payroll_item_id)
            ->where('description', $description);

        if ($empAllowance->exists()) {
            throw ValidationException::withMessages([
                'a_description' => 'Allowance is already specified.',
            ]);
        }

        $newAllowance = Allowance::where('description', $description);
        if (! $newAllowance->exists()) {
            Allowance::create([
                'description' => $description,
            ]);
        }

        $allowance = EmployeeAllowance::create([
            'payroll_item_id' => $request->payroll_item_id,
            'description' => $description,
            'amount' => $request->a_amount,
        ]);

        return back()->with('status', "$description added to allowances.");

    }

    public function deleteEmployeeAllowance(Request $request, $allowance_id)
    {
        $allowance = EmployeeAllowance::find($allowance_id);
        $description = $allowance->description;
        $allowance->delete();

        return back()->with('status', "$description has been deleted from allowances.");
    }

    public function saveEmployeeDeduction(Request $request)
    {
        $request->validate([
            'd_description' => ['required'],
            'd_other' => ['required_if:d_description,Other'],
            'd_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'min:1'],
        ], [
            'd_other.required_if' => 'Please specify the other deduction.',
            'd_amount.regex' => 'Please specify a valid amount.',
            'd_amount.min' => 'Amount at least 1.',
        ]);

        $description = $request->d_description === 'Other' ? $request->d_other : $request->d_description;

        $empDeduction = EmployeeDeduction::where('payroll_item_id', $request->payroll_item_id)
            ->where('description', $description);

        if ($empDeduction->exists()) {
            throw ValidationException::withMessages([
                'd_description' => 'Deduction is already specified.',
            ]);
        }

        $newDeduction = Deduction::where('description', $description);
        if (! $newDeduction->exists()) {
            Deduction::create([
                'description' => $description,
            ]);
        }

        $deduction = EmployeeDeduction::create([
            'payroll_item_id' => $request->payroll_item_id,
            'description' => $description,
            'amount' => $request->d_amount,
        ]);

        return back()->with('status', "$description added to deductions.");
    }

    public function deleteEmployeeDeduction(Request $request, $deduction_id)
    {
        $deduction = EmployeeDeduction::find($deduction_id);
        $description = $deduction->description;
        $deduction->delete();

        return back()->with('status', "$description has been deleted from deductions.");
    }

    // Allowances
    public function allowances()
    {
        $allowances = Allowance::with(['positions', 'employees'])->get();
        $positions = Position::all();
        $employees = Employee::orderBy('lastname')->get();

        return view('payroll.allowances.index', compact('allowances', 'positions', 'employees'));
    }

    public function saveAllowance(Request $request)
    {
        $request->validate([
            'description' => [
                'required', 'string', 'min:3', 'unique:allowances,description',
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'scope' => ['required', 'in:all,position,employee'],
            'amount' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'fixed' && $request->scope === 'all'),
                'numeric', 'min:0',
            ],
            'percentage' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'percentage' && $request->scope === 'all'),
                'numeric', 'between:0,100',
            ],
            'schedule' => ['required', 'in:every_payroll,specific_month'],
            'target_month' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'between:1,12'],
            'target_year' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'min:2020', 'max:2099'],
        ]);

        $allowance = Allowance::create([
            'description' => $request->description,
            'type' => $request->type,
            'scope' => $request->scope,
            'amount' => $request->amount,
            'percentage' => $request->percentage,
            'schedule' => $request->schedule,
            'target_month' => $request->schedule === 'specific_month' ? $request->target_month : null,
            'target_year' => $request->schedule === 'specific_month' ? $request->target_year : null,
        ]);

        if ($request->scope == 'position') {
            foreach ($request->position_amounts as $id => $val) {
                if ($val > 0) {
                    $pivotData = $request->type == 'fixed' ? ['amount' => $val] : ['percentage' => $val];
                    $allowance->positions()->attach($id, $pivotData);
                }
            }
        } elseif ($request->scope == 'employee') {
            foreach ($request->employee_amounts as $id => $val) {
                if ($val > 0) {
                    $pivotData = $request->type == 'fixed' ? ['amount' => $val] : ['percentage' => $val];
                    $allowance->employees()->attach($id, $pivotData);
                }
            }
        }

        return redirect()->route('payroll.allowances')->with('status', 'Saved.');
    }

    public function updateAllowance(Request $request, $id)
    {
        $request->validate([
            'description' => [
                'required', 'string', 'min:3',
                Rule::unique('allowances', 'description')->ignore($id),
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'scope' => ['required', 'in:all,position,employee'],
            'amount' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'fixed' && $request->scope === 'all'),
                'numeric', 'min:0',
            ],
            'percentage' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'percentage' && $request->scope === 'all'),
                'numeric', 'between:0,100',
            ],
            'schedule' => ['required', 'in:every_payroll,specific_month'],
            'target_month' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'between:1,12'],
            'target_year' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'min:2020', 'max:2099'],
        ]);

        $allowance = Allowance::find($id);
        $allowance->update([
            'description' => $request->description,
            'type' => $request->type,
            'scope' => $request->scope,
            'amount' => $request->amount,
            'percentage' => $request->percentage,
            'schedule' => $request->schedule,
            'target_month' => $request->schedule === 'specific_month' ? $request->target_month : null,
            'target_year' => $request->schedule === 'specific_month' ? $request->target_year : null,
        ]);

        if ($request->scope === 'position') {
            if ($request->has('position_amounts')) {
                $syncData = [];
                foreach ($request->position_amounts as $pid => $val) {
                    if ($val > 0) {
                        $data = [];
                        if ($request->type == 'fixed') {
                            $data['amount'] = $val;
                        } else {
                            $data['percentage'] = $val;
                            $data['amount'] = 0;
                        }
                        $syncData[$pid] = $data;
                    }
                }
                $allowance->positions()->sync($syncData);
            }
        } else {
            $allowance->positions()->detach();
        }

        if ($request->scope === 'employee') {
            if ($request->has('employee_amounts')) {
                $syncData = [];
                foreach ($request->employee_amounts as $eid => $val) {
                    if ($val > 0) {
                        $data = [];
                        if ($request->type == 'fixed') {
                            $data['amount'] = $val;
                        } else {
                            $data['percentage'] = $val;
                            $data['amount'] = 0;
                        }
                        $syncData[$eid] = $data;
                    }
                }
                $allowance->employees()->sync($syncData);
            }
        } else {
            $allowance->employees()->detach();
        }

        return redirect()->route('payroll.allowances')->with('status', 'Updated.');
    }

    public function deleteAllowance(Request $request, $id)
    {
        $allowance = Allowance::find($id);
        $allowance->delete();

        return redirect()->route('payroll.allowances')->with('status', 'Deleted.');
    }

    // Deduction
    public function deductions()
    {
        $deductions = Deduction::with(['positions', 'employees'])->get();
        $positions = Position::all();
        $employees = Employee::orderBy('lastname')->get();

        return view('payroll.deductions.index', compact('deductions', 'positions', 'employees'));
    }

    public function saveDeduction(Request $request)
    {
        $request->validate([
            'description' => [
                'required', 'string', 'min:3', 'unique:deductions,description',
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'scope' => ['required', 'in:all,position,employee'],
            'amount' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'fixed' && $request->scope === 'all'),
                'numeric', 'min:0',
            ],
            'percentage' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'percentage' && $request->scope === 'all'),
                'numeric', 'between:0,100',
            ],
            'schedule' => ['required', 'in:every_payroll,specific_month'],
            'target_month' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'between:1,12'],
        ]);

        $deduction = Deduction::create([
            'description' => $request->description,
            'type' => $request->type,
            'scope' => $request->scope,
            'amount' => ($request->scope == 'all' && $request->type == 'fixed') ? $request->amount : null,
            'percentage' => ($request->scope == 'all' && $request->type == 'percentage') ? $request->percentage : null,
            'schedule' => $request->schedule,
            'target_month' => ($request->schedule == 'specific_month') ? $request->target_month : null,
        ]);

        if ($request->scope === 'position' && $request->has('position_amounts')) {
            $syncData = [];
            foreach ($request->position_amounts as $id => $val) {
                if ($val > 0) {
                    $data = [];
                    if ($request->type == 'fixed') {
                        $data['amount'] = $val;
                    } else {
                        $data['percentage'] = $val;
                        $data['amount'] = 0;
                    }
                    $syncData[$id] = $data;
                }
            }
            $deduction->positions()->sync($syncData);
        } elseif ($request->scope === 'employee' && $request->has('employee_amounts')) {
            $syncData = [];
            foreach ($request->employee_amounts as $id => $val) {
                if ($val > 0) {
                    $data = [];
                    if ($request->type == 'fixed') {
                        $data['amount'] = $val;
                    } else {
                        $data['percentage'] = $val;
                        $data['amount'] = 0;
                    }
                    $syncData[$id] = $data;
                }
            }
            $deduction->employees()->sync($syncData);
        }

        return redirect()->route('payroll.deductions')->with('status', 'Saved.');
    }

    public function updateDeduction(Request $request, $id)
    {
        $request->validate([
            'description' => [
                'required', 'string', 'min:3',
                Rule::unique('deductions', 'description')->ignore($id),
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'scope' => ['required', 'in:all,position,employee'],
            'amount' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'fixed' && $request->scope === 'all'),
                'numeric', 'min:0',
            ],
            'percentage' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'percentage' && $request->scope === 'all'),
                'numeric', 'between:0,100',
            ],
            'schedule' => ['required', 'in:every_payroll,specific_month'],
            'target_month' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'between:1,12'],
        ]);

        $deduction = Deduction::find($id);
        $deduction->update([
            'description' => $request->description,
            'type' => $request->type,
            'scope' => $request->scope,
            'amount' => ($request->scope == 'all' && $request->type == 'fixed') ? $request->amount : null,
            'percentage' => ($request->scope == 'all' && $request->type == 'percentage') ? $request->percentage : null,
            'schedule' => $request->schedule,
            'target_month' => ($request->schedule == 'specific_month') ? $request->target_month : null,
        ]);

        if ($request->scope === 'position') {
            if ($request->has('position_amounts')) {
                $syncData = [];
                foreach ($request->position_amounts as $pid => $val) {
                    if ($val > 0) {
                        $data = [];
                        if ($request->type == 'fixed') {
                            $data['amount'] = $val;
                        } else {
                            $data['percentage'] = $val;
                            $data['amount'] = 0;
                        }
                        $syncData[$pid] = $data;
                    }
                }
                $deduction->positions()->sync($syncData);
            }
        } else {
            $deduction->positions()->detach();
        }

        if ($request->scope === 'employee') {
            if ($request->has('employee_amounts')) {
                $syncData = [];
                foreach ($request->employee_amounts as $eid => $val) {
                    if ($val > 0) {
                        $data = [];
                        if ($request->type == 'fixed') {
                            $data['amount'] = $val;
                        } else {
                            $data['percentage'] = $val;
                            $data['amount'] = 0;
                        }
                        $syncData[$eid] = $data;
                    }
                }
                $deduction->employees()->sync($syncData);
            }
        } else {
            $deduction->employees()->detach();
        }

        return redirect()->route('payroll.deductions')->with('status', 'Updated.');
    }

    public function deleteDeduction(Request $request, $id)
    {
        $deduction = Deduction::find($id);
        $deduction->delete();

        return redirect()->route('payroll.deductions')->with('status', 'Deleted.');
    }

    public function generatePayrollReport(Request $request)
    {
        $payroll = Payroll::find($request->payroll_id);

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('payroll.report.payroll', compact('payroll'))->render());
        $dompdf->setPaper('folio', 'landscape');
        $dompdf->render();

        return $dompdf->stream('payroll-'
            .$payroll->department->name.'-'
            .$payroll->date_from.'--'.$payroll->date_to.'.pdf'
        );
    }

    public function generatePayslipReport(Request $request)
    {
        $payroll = Payroll::find($request->payroll_id);

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $preparedBy = auth()->user();
        $dompdf->loadHtml(view('payroll.report.payslips', compact('payroll', 'preparedBy'))->render());
        $dompdf->setPaper('folio', 'portrait');
        $dompdf->render();

        return $dompdf->stream('payslips-'
            .$payroll->department->name.'-'
            .$payroll->date_from.'--'.$payroll->date_to.'.pdf'
        );
    }

    public function generateSummaryReport(Request $request)
    {
        $payroll = Payroll::findOrFail($request->payroll_id);
        $department = Department::find($payroll->department_id);

        $from = Carbon::parse($payroll->date_from);
        $to = Carbon::parse($payroll->date_to);
        $dates = CarbonPeriod::create($from, $to);

        $data = [];

        // Iterate through payroll items to ensure we include employees actually in this payroll
        foreach ($payroll->items as $item) {
            $e = $item->employee;
            $logs = $e->dtrRange($from->format('Y-m-d'), $to->format('Y-m-d'))->keyBy('log_date');

            $row = [
                'name' => $e->full_name,
                'position' => $e->position->description,
                'days' => [],
                'total_days' => 0,
                'total_ot' => 0,
                'total_undertime' => 0,
            ];

            foreach ($dates as $date) {
                $dateStr = $date->format('Y-m-d');
                $val = 0;
                $amVal = 0;
                $pmVal = 0;

                if ($logs->has($dateStr)) {
                    $log = $logs[$dateStr];

                    // Simple logic: AM session + PM session
                    if ($log->am_in && $log->am_out) {
                        $amVal = 1;
                        $val += 0.5;
                    }
                    if ($log->pm_in && $log->pm_out) {
                        $pmVal = 1;
                        $val += 0.5;
                    }

                    // Calculate Daily OT (minutes)
                    $ot = $e->dailyOvertime($log);
                    $row['total_ot'] += $ot;

                    // Calculate Daily Undertime (minutes)
                    $tardiness = $e->dailyTardiness($log)['tardiness'];
                    $row['total_undertime'] += $tardiness;
                }

                $row['days'][$dateStr] = ['am' => $amVal, 'pm' => $pmVal];
                $row['total_days'] += $val;
            }

            $data[] = $row;
        }

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dateCounts = ($from->diffInDays($to) + 1) * 2; // Double the count for AM/PM columns
        $dompdf->loadHtml(view('payroll.report.summary', compact('payroll', 'dates', 'data', 'dateCounts'))->render());
        $dompdf->setPaper('folio', 'landscape');
        $dompdf->render();

        return $dompdf->stream('summary-'.$payroll->department->name.'.pdf');
    }

    public function regeneratePayroll(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        // Delete all existing payroll items (cascades to allowances and deductions)
        PayrollItem::where('payroll_id', $payroll->id)->delete();

        $from = $payroll->date_from;
        $to = $payroll->date_to;
        $department = Department::find($payroll->department_id);

        $allAllowances = Allowance::with(['positions', 'employees'])->get();
        $allDeductions = Deduction::with(['positions', 'employees'])->get();

        $query = Employee::where('department_id', $department->id);

        if ($payroll->salary_type) {
            $query->where('salary_type', $payroll->salary_type);
        }

        $employees = $query->get();

        foreach ($employees as $e) {
            $numDays = $e->numberOfDutyDays($from, $to);
            $overtime = $e->overtime($from, $to);
            $tardiness = $e->tardiness($from, $to);
            $dailyRate = $e->daily_rate;
            $overtime_pay = $overtime * $e->minutely_rate;

            // Calculate Holiday Pay
            $holidays = Holiday::whereBetween('date', [$from, $to])->get()->keyBy('date');
            $holidayShifts = Shift::where('is_holiday', true)->get()->keyBy('name');
            $logs = $e->dtrRange($from, $to);
            $holidayPay = 0;

            foreach ($logs as $log) {
                if ($holidays->has($log->log_date)) {
                    $holiday = $holidays[$log->log_date];
                    // Find corresponding shift for the holiday type
                    $shiftRate = 100;
                    if ($holidayShifts->has($holiday->type)) {
                        $shiftRate = $holidayShifts[$holiday->type]->rate_percentage;
                    }

                    if ($shiftRate > 100) {
                        // Extra pay = DailyRate * ((Rate% - 100) / 100)
                        $extraPercentage = ($shiftRate - 100) / 100;
                        $holidayPay += $dailyRate * $extraPercentage;
                    }
                }
            }

            $gross = ($numDays * $dailyRate) + $overtime_pay + $holidayPay;

            $tHour = floor($tardiness['grandTotal'] / 60);
            $tMins = $tardiness['grandTotal'] % 60;
            $tAmount = $tardiness['grandTotal'] * $e->minutely_rate;

            $payrollItem = PayrollItem::create([
                'payroll_id' => $payroll->id,
                'employee_id' => $e->id,
                'num_of_days' => $numDays,
                'daily_rate' => $e->daily_rate,
                'overtime' => $overtime,
                'overtime_pay' => $overtime_pay,
                'gross_pay' => $gross,
                'net_pay' => $gross,
            ]);

            // Auto-add Allowances
            foreach ($allAllowances as $allowance) {
                // Check Schedule
                $payrollMonth = \Carbon\Carbon::parse($to)->month;
                if ($allowance->schedule === 'specific_month' && $allowance->target_month != $payrollMonth) {
                    continue;
                }

                $amount = 0;
                $percentage = 0;
                $shouldApply = false;

                if ($allowance->scope === 'all') {
                    $shouldApply = true;
                    if ($allowance->type === 'fixed') {
                        $amount = $allowance->amount;
                    } elseif ($allowance->type === 'percentage') {
                        $percentage = $allowance->percentage;
                    }
                } elseif ($allowance->scope === 'position') {
                    $pivot = $allowance->positions->where('id', $e->position_id)->first();
                    if ($pivot) {
                        $shouldApply = true;
                        if ($allowance->type === 'fixed') {
                            $amount = $pivot->pivot->amount;
                        } elseif ($allowance->type === 'percentage') {
                            $percentage = $pivot->pivot->percentage;
                        }
                    }
                } elseif ($allowance->scope === 'employee') {
                    $pivot = $allowance->employees->where('id', $e->id)->first();
                    if ($pivot) {
                        $shouldApply = true;
                        if ($allowance->type === 'fixed') {
                            $amount = $pivot->pivot->amount;
                        } elseif ($allowance->type === 'percentage') {
                            $percentage = $pivot->pivot->percentage;
                        }
                    }
                }

                if ($shouldApply) {
                    // Calculate Amount if Percentage
                    if ($allowance->type === 'percentage' && $percentage > 0) {
                        $basicPay = $numDays * $dailyRate;
                        $amount = $basicPay * ($percentage / 100);
                    }

                    if ($amount > 0) {
                        EmployeeAllowance::create([
                            'payroll_item_id' => $payrollItem->id,
                            'description' => $allowance->description,
                            'amount' => $amount,
                        ]);
                    }
                }
            }

            // Auto-add Deductions
            foreach ($allDeductions as $deduction) {
                // Check Schedule
                $payrollMonth = \Carbon\Carbon::parse($to)->month;
                if ($deduction->schedule === 'specific_month' && $deduction->target_month != $payrollMonth) {
                    continue;
                }

                $amount = 0;
                $percentage = 0;
                $shouldApply = false;

                if ($deduction->scope === 'all') {
                    $shouldApply = true;
                    if ($deduction->type === 'fixed') {
                        $amount = $deduction->amount;
                    } elseif ($deduction->type === 'percentage') {
                        $percentage = $deduction->percentage;
                    }
                } elseif ($deduction->scope === 'position') {
                    $pivot = $deduction->positions->where('id', $e->position_id)->first();
                    if ($pivot) {
                        $shouldApply = true;
                        if ($deduction->type === 'fixed') {
                            $amount = $pivot->pivot->amount;
                        } elseif ($deduction->type === 'percentage') {
                            $percentage = $pivot->pivot->percentage;
                        }
                    }
                } elseif ($deduction->scope === 'employee') {
                    $pivot = $deduction->employees->where('id', $e->id)->first();
                    if ($pivot) {
                        $shouldApply = true;
                        if ($deduction->type === 'fixed') {
                            $amount = $pivot->pivot->amount;
                        } elseif ($deduction->type === 'percentage') {
                            $percentage = $pivot->pivot->percentage;
                        }
                    }
                }

                if ($shouldApply) {
                    // Calculate Amount if Percentage
                    if ($deduction->type === 'percentage' && $percentage > 0) {
                        $basicPay = $numDays * $dailyRate;
                        $amount = $basicPay * ($percentage / 100);
                    }

                    if ($amount > 0) {
                        EmployeeDeduction::create([
                            'payroll_item_id' => $payrollItem->id,
                            'description' => $deduction->description,
                            'amount' => $amount,
                        ]);
                    }
                }
            }

            EmployeeDeduction::create([
                'payroll_item_id' => $payrollItem->id,
                'description' => "Tardiness {$tHour}h, {$tMins}m",
                'amount' => $tAmount,
            ]);

            if ($holidayPay > 0) {
                EmployeeAllowance::create([
                    'payroll_item_id' => $payrollItem->id,
                    'description' => 'Holiday Pay',
                    'amount' => $holidayPay,
                ]);
            }

            // Recalculate and update Net Pay in DB
            $payrollItem->update([
                'net_pay' => $payrollItem->netPay(),
            ]);
        }

        return redirect()->route('payroll.view', $payroll->id)
            ->with('status', 'Payroll regenerated successfully based on current DTR data.');
    }
}
