<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Employee;

class EmployeeAuthController extends Controller
{
    public function index()
    {
        return view('pages.employees.login');
    }

    public function forgotPassword() 
    {
        return view('pages.employees.forgot-password');
    }
    
    public function emailPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Send reset link using "admins" password broker
        $status = Password::broker('employees')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }
    
    public function resetPassword(string $token, Request $request) 
    {
        return view('pages.employees.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }
    
    public function saveResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::broker('employees')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('employee.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function login(Request $request)
    {
        $user = Employee::where('email', $request->email)->first();

        // 2. I-check kung naay user UG husto ang password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        Auth::guard('employee')->login($user, $request->boolean('remember'));   
        $request->session()->regenerate();
        return redirect()->route('employee.home');
    }

    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('employee.login');
    }

}
