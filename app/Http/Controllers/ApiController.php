<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Dtr;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function makeLog(Request $request)
    {
        // ✅ Allowed columns (SECURITY)
        $allowedMarks = [
            'am_in', 'am_out',
            'pm_in', 'pm_out',
            'ot_in', 'ot_out',
        ];
    
        $employeeId = $request->employee_id;
        $mark = $request->mark;
    
        if (!in_array($mark, $allowedMarks)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid log type.',
            ], 422);
        }
    
        $today = $request->date_log;
    
        // ✅ Create or get today's DTR
        $dtr = Dtr::firstOrCreate([
            'employee_id' => $employeeId,
            'log_date' => $today,
        ]);
    
        // ✅ Check if already logged
        if (!is_null($dtr->$mark)) {
            return response()->json([
                'status' => 'duplicate',
                'message' => strtoupper(str_replace('_', ' ', $mark)) . ' already recorded.',
                'data' => [
                    'employee_id' => $employeeId,
                    'fullname' => $dtr->employee->full_name,
                    'log_date' => $today,
                    'time' => $dtr->$mark,
                ],
            ], 200);
        }
    
        // ✅ Save log
        $dtr->$mark = $request->time_log;
        $dtr->save();
    
        return response()->json([
            'status' => 'success',
            'message' => strtoupper(str_replace('_', ' ', $mark)) . ' recorded successfully.',
            'data' => [
                'employee_id' => $employeeId,
                'fullname' => $dtr->employee->full_name,
                'log_date' => $today,
                'time' => Carbon::parse($request->time_log)->format('h:i A'),
            ],
        ], 200);
    }


    public function viewDTR(Request $request)
    {
        $id = $request->employee_id;
        $from = $request->date_from;
        $to = $request->date_to;

        $employee = Employee::find($id);

        return view('dtr.view', [
            'employee' => $employee,
            'from' => $from,
            'to' => $to
        ]);
    }

    public function verifyTimekeeper(Request $request)
    {
        $user = User::where('email', $request->email)
                    ->where('role', 'timekeeper')
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Access denied. Email not found.'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Incorrect password.'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Timekeeper verified successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);

    }

}
