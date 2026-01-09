<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeAllowance;
use App\Models\Position;
use App\Models\Holiday;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use Dompdf\Dompdf;
use Dompdf\Options;

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
        return view('payroll.create', compact('departments'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from']
        ], [
            'date_from.required' => 'This is required.',
            'date_from.date' => 'This must be a valid date.',
            'date_to.required' => 'This is required.',
            'date_to.date' => 'This must be a valid date.',
            'date_to.after_or_equal' => 'This must after or equal to date from.',
        ]);

        $payrollExists = Payroll::where('department_id', $request->department)
                          ->where('date_from', $request->date_from)
                          ->where('date_to', $request->date_to)
                          ->exists();

        if ($payrollExists) {
            throw ValidationException::withMessages([
                'department' => 'A payroll record for this department and date range already exists.'
            ]);
        }

        $payroll = Payroll::create([
            'department_id' => $request->department,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ]);

        $from = $request->date_from;
        $to = $request->date_to;

        $department = Department::find($request->department);

        $allAllowances = Allowance::with(['positions', 'employees'])->get();
        $allDeductions = Deduction::with(['positions', 'employees'])->get();

        foreach ($department->employees as $e) {

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
                'net_pay' => $gross
            ]);

            // Auto-add Allowances
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
                            'amount' => $amount
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
                            'amount' => $amount
                        ]);
                    }
                }
            }

            EmployeeDeduction::create([
                'payroll_item_id' => $payrollItem->id,
                'description' => "Tardiness {$tHour}h, {$tMins}m",
                'amount' => $tAmount
            ]);

            if ($holidayPay > 0) {
                EmployeeAllowance::create([
                    'payroll_item_id' => $payrollItem->id,
                    'description' => "Holiday Pay",
                    'amount' => $holidayPay
                ]);
            }
        }   

        return redirect()->route('payroll.view', $payroll->id)->with('status', 'Payroll for '.$department->name.' has been created.');
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
            'a_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'min:1']
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
                'a_description' => 'Allowance is already specified.'
            ]);
        }

        $newAllowance = Allowance::where('description', $description);
        if (!$newAllowance->exists()) {
            Allowance::create([
                'description' => $description
            ]);
        }

        $allowance = EmployeeAllowance::create([
            'payroll_item_id' => $request->payroll_item_id,
            'description' => $description,
            'amount' => $request->a_amount
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
            'd_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'min:1']
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
                'd_description' => 'Deduction is already specified.'
            ]);
        }

        $newDeduction = Deduction::where('description', $description);
        if (!$newDeduction->exists()) {
            Deduction::create([
                'description' => $description
            ]);
        }

        $deduction = EmployeeDeduction::create([
            'payroll_item_id' => $request->payroll_item_id,
            'description' => $description,
            'amount' => $request->d_amount
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
                'required', 'string', 'min:3', 'unique:allowances,description'
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'scope' => ['required', 'in:all,position,employee'],
            'amount' => [
                'nullable', 
                Rule::requiredIf(fn() => $request->type === 'fixed' && $request->scope === 'all'),
                'numeric', 'min:0'
            ],
            'percentage' => [
                'nullable', 
                Rule::requiredIf(fn() => $request->type === 'percentage' && $request->scope === 'all'),
                'numeric', 'between:0,100'
            ],
            'schedule' => ['required', 'in:every_payroll,specific_month'],
            'target_month' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'between:1,12']
        ]);

        $allowance = Allowance::create([
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
            $allowance->positions()->sync($syncData);
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
            $allowance->employees()->sync($syncData);
        }

        return redirect()->route('payroll.allowances')->with('status', 'Saved.');
    }

    public function updateAllowance(Request $request, $id) 
    {
        $request->validate([
            'description' => [
                'required', 'string', 'min:3',
                Rule::unique('allowances', 'description')->ignore($id)
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'scope' => ['required', 'in:all,position,employee'],
            'amount' => [
                'nullable', 
                Rule::requiredIf(fn() => $request->type === 'fixed' && $request->scope === 'all'),
                'numeric', 'min:0'
            ],
            'percentage' => [
                'nullable', 
                Rule::requiredIf(fn() => $request->type === 'percentage' && $request->scope === 'all'),
                'numeric', 'between:0,100'
            ],
            'schedule' => ['required', 'in:every_payroll,specific_month'],
            'target_month' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'between:1,12']
        ]);

        $allowance = Allowance::find($id);
        $allowance->update([
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
                'required', 'string', 'min:3', 'unique:deductions,description'
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'scope' => ['required', 'in:all,position,employee'],
            'amount' => [
                'nullable', 
                Rule::requiredIf(fn() => $request->type === 'fixed' && $request->scope === 'all'),
                'numeric', 'min:0'
            ],
            'percentage' => [
                'nullable', 
                Rule::requiredIf(fn() => $request->type === 'percentage' && $request->scope === 'all'),
                'numeric', 'between:0,100'
            ],
            'schedule' => ['required', 'in:every_payroll,specific_month'],
            'target_month' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'between:1,12']
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
                Rule::unique('deductions', 'description')->ignore($id)
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'scope' => ['required', 'in:all,position,employee'],
            'amount' => [
                'nullable', 
                Rule::requiredIf(fn() => $request->type === 'fixed' && $request->scope === 'all'),
                'numeric', 'min:0'
            ],
            'percentage' => [
                'nullable', 
                Rule::requiredIf(fn() => $request->type === 'percentage' && $request->scope === 'all'),
                'numeric', 'between:0,100'
            ],
            'schedule' => ['required', 'in:every_payroll,specific_month'],
            'target_month' => ['nullable', 'required_if:schedule,specific_month', 'integer', 'between:1,12']
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

        $options = new Options();
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

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('payroll.report.payslips', compact('payroll'))->render());
        $dompdf->setPaper('folio', 'portrait');
        $dompdf->render();

        return $dompdf->stream('payslips-'
            .$payroll->department->name.'-'
            .$payroll->date_from.'--'.$payroll->date_to.'.pdf'
        );
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

        foreach ($department->employees as $e) {

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
                'net_pay' => $gross
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
                            'amount' => $amount
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
                            'amount' => $amount
                        ]);
                    }
                }
            }

            EmployeeDeduction::create([
                'payroll_item_id' => $payrollItem->id,
                'description' => "Tardiness {$tHour}h, {$tMins}m",
                'amount' => $tAmount
            ]);

            if ($holidayPay > 0) {
                EmployeeAllowance::create([
                    'payroll_item_id' => $payrollItem->id,
                    'description' => "Holiday Pay",
                    'amount' => $holidayPay
                ]);
            }
        }   

        return redirect()->route('payroll.view', $payroll->id)
            ->with('status', 'Payroll regenerated successfully based on current DTR data.');
    }

}
