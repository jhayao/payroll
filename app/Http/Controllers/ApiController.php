<?php

namespace App\Http\Controllers;

use App\Models\Dtr;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        if (! in_array($mark, $allowedMarks)) {
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
        if (! is_null($dtr->$mark)) {
            return response()->json([
                'status' => 'duplicate',
                'message' => strtoupper(str_replace('_', ' ', $mark)).' already recorded.',
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
            'message' => strtoupper(str_replace('_', ' ', $mark)).' recorded successfully.',
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
            'to' => $to,
        ]);
    }

    public function verifyTimekeeper(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('role', 'timekeeper')
            ->first();

        if (! $user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Access denied. Email not found.',
            ], 404);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Incorrect password.',
            ], 401);
        }

        // Find linked Employee record by email
        $employee = Employee::where('email', $user->email)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Timekeeper verified successfully.',
            'user' => [
                'id' => $user->id,
                'employee_id' => $employee ? $employee->id : null, // The Employee Model ID (PK)
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);

    }

    public function getEmployees()
    {
        $employees = Employee::all()->map(function ($e) {
            return [
                'id' => $e->id,
                'name' => $e->full_name,
                'photo_url' => $e->photo_2x2 ? asset($e->photo_2x2) : null,
                'face_photos' => [
                    $e->photo_lg ? asset($e->photo_lg) : null,
                    $e->photo_lg2 ? asset($e->photo_lg2) : null,
                    $e->photo_lg3 ? asset($e->photo_lg3) : null,
                ],
            ];
        });

        return response()->json($employees);
    }

    public function getTimekeeperProjects(Request $request) 
    {
        $timeKeeperId = $request->timekeeper_id;
        
        $projects = \App\Models\Project::where('time_keeper_id', $timeKeeperId)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'status' => $p->status,
                    'start_date' => $p->start_date ? $p->start_date->format('Y-m-d') : null,
                    'end_date' => $p->end_date ? $p->end_date->format('Y-m-d') : null,
                ];
            });

        return response()->json($projects);
    }

    public function getTimekeeperEmployees(Request $request)
    {
        $timeKeeperId = $request->timekeeper_id;
        $projectId = $request->project_id; // Optional filter

        $query = \App\Models\Project::where('time_keeper_id', $timeKeeperId);

        if ($projectId) {
            $query->where('id', $projectId);
        }

        $projects = $query->with('employees')->get();
        
        // Collect all unique employees
        $employees = collect();
        foreach ($projects as $project) {
            foreach ($project->employees as $employee) {
                if (!$employees->contains('id', $employee->id)) {
                    $employees->push($employee);
                }
            }
        }

        $data = $employees->map(function($e) {
            return [
                'id' => $e->id,
                'name' => $e->full_name,
                'photo_url' => $e->photo_2x2 ? asset($e->photo_2x2) : null,
                'position' => $e->position->description ?? 'N/A',
                'face_photos' => [
                    $e->photo_lg ? asset($e->photo_lg) : null,
                    $e->photo_lg2 ? asset($e->photo_lg2) : null,
                    $e->photo_lg3 ? asset($e->photo_lg3) : null,
                ],
            ];
        })->values();

        return response()->json($data);
    }

    public function getTimekeeperAttendance(Request $request)
    {
        $timeKeeperId = $request->timekeeper_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $projectId = $request->project_id; // Optional filter

        // 1. Get Employees managed by this Timekeeper
        $projectQuery = \App\Models\Project::where('time_keeper_id', $timeKeeperId);
        if ($projectId) {
            $projectQuery->where('id', $projectId);
        }
        $projects = $projectQuery->with('employees')->get();

        $employeeIds = collect();
        foreach ($projects as $project) {
            $employeeIds = $employeeIds->merge($project->employees->pluck('id'));
        }
        $employeeIds = $employeeIds->unique();

        // 2. Fetch DTRs for these employees within date range
        $dtrs = Dtr::whereIn('employee_id', $employeeIds)
            ->whereBetween('log_date', [$dateFrom, $dateTo])
            ->with(['employee' => function($q) {
                $q->select('id', 'lastname', 'firstname', 'middlename', 'suffix'); // Select minimal columns
            }])
            ->orderBy('log_date', 'desc')
            ->get()
            ->map(function($dtr) {
                return [
                    'id' => $dtr->id,
                    'employee_id' => $dtr->employee_id,
                    'employee_name' => $dtr->employee->full_name,
                    'log_date' => $dtr->log_date,
                    'am_in' => $dtr->am_in,
                    'am_out' => $dtr->am_out,
                    'pm_in' => $dtr->pm_in,
                    'pm_out' => $dtr->pm_out,
                    'ot_in' => $dtr->ot_in,
                    'ot_out' => $dtr->ot_out,
                ];
            });

        return response()->json($dtrs);
    }
}
