<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Dtr;
use App\Models\Employee;

class EmployeeController extends Controller
{

    public function index()
    {
        $dtr = Dtr::where('employee_id', auth()->user()->id)
                  ->orderBy('id', 'desc')
                  ->limit(5)
                  ->get();
        return view('pages.employees.home', compact('dtr'));
    }

    public function dtr()
    {
        return view('pages.employees.dtr');
    }

    public function profile()
    {
        $employee = Employee::with(['position', 'department'])->find(auth()->user()->id);
        return view('pages.employees.profile', compact('employee'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();
        return back()->with('status', 'Password changed successfully.');
    }
   
}
