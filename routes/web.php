<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\EmployeeAuthController;

use App\Models\Employee;

Route::get('/', function() {
    return redirect()->route('login');
});

Route::get('/ir', function () {
    $employees = Employee::all();
    return view('face-portal', compact('employees'));
});

Route::middleware(['auth', 'role:admin,hr,accounting'])->group(function() {
    Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('dtr', [AdminController::class, 'dtr'])->name('dtr');
    Route::post('dtr/generate-report', [AdminController::class, 'generateDTRReport'])->name('dtr.generate');
});

Route::middleware(['auth', 'role:admin,hr'])->group(function() {
    Route::get('employees', [AdminController::class, 'employees'])->name('employees');
    Route::prefix('employees/')->group(function () {
        Route::get('add', [AdminController::class, 'createEmployee'])->name('employees.add');
        Route::post('add', [AdminController::class, 'saveEmployee'])->name('employees.save');
        Route::get('{id}/view', [AdminController::class, 'viewEmployee'])->name('employees.view');
        Route::post('{id}/view', [AdminController::class, 'updateEmployee'])->name('employees.update');
        Route::post('{id}/update-photos', [AdminController::class, 'updatePhotos'])->name('employees.update-photo');
        Route::post('{id}/update-password', [AdminController::class, 'updatePassword'])->name('employees.update-password');
        Route::post('shift/{id}/update', [AdminController::class, 'updateEmployeeShift'])->name('employees.update-shift');
        Route::delete('{id}/delete', [AdminController::class, 'deleteEmployee'])->name('employees.delete');
    });

    // Departments
    Route::get('departments', [AdminController::class, 'departments'])->name('departments');
    Route::prefix('departments/')->group(function () {
        Route::controller(AdminController::class)->group(function() {
            Route::get('add', 'addDepartment')->name('departments.add');
            Route::post('add', 'saveDepartment')->name('departments.save');
            Route::get('{id}/edit', 'editDepartment')->name('departments.edit');
            Route::post('{id}/edit', 'updateDepartment')->name('departments.update');
            Route::delete('{id}/delete', 'deleteDepartment')->name('departments.delete');
        });
    });

    // Positions
    Route::get('positions', [AdminController::class, 'positions'])->name('positions');
    Route::prefix('positions/')->group(function () {
        Route::controller(AdminController::class)->group(function() {
            Route::get('add', 'addPosition')->name('positions.add');
            Route::post('add', 'savePosition')->name('positions.save');
            Route::get('{id}/edit', 'editPosition')->name('positions.edit');
            Route::post('{id}/edit', 'updatePosition')->name('positions.update');
            Route::delete('{id}/delete', 'deletePosition')->name('positions.delete');
        });
    });

    // Shifts
    Route::get('shifts', [AdminController::class, 'shifts'])->name('shifts');
    Route::prefix('shifts/')->group(function () {
        Route::controller(AdminController::class)->group(function() {
            Route::get('add', 'addShift')->name('shifts.add');
            Route::post('add', 'saveShift')->name('shifts.save');
            Route::get('{id}/edit', 'editShift')->name('shifts.edit');
            Route::post('{id}/edit', 'updateShift')->name('shifts.update');
            Route::delete('{id}/delete', 'deleteShift')->name('shifts.delete');
        });
    });

    // Projects
    Route::get('projects', [AdminController::class, 'projects'])->name('projects');
    Route::prefix('projects/')->group(function () {
        Route::controller(AdminController::class)->group(function() {
            Route::get('add', 'addProject')->name('projects.add');
            Route::post('add', 'saveProject')->name('projects.save');
            Route::get('{id}', 'viewProject')->name('projects.view');
            Route::get('{id}/edit', 'editProject')->name('projects.edit');
            Route::post('{id}/update', 'updateProject')->name('projects.update');
            Route::delete('{id}/delete', 'deleteProject')->name('projects.delete');
            Route::post('{id}/assign-employee', 'assignEmployee')->name('projects.assign-employee');
            Route::delete('{id}/remove-employee/{employee_id}', 'removeEmployee')->name('projects.remove-employee');
        });
    });

    // Calendar
    Route::controller(\App\Http\Controllers\CalendarController::class)->prefix('calendar')->group(function() {
       Route::get('/', 'index')->name('calendar.index'); 
       Route::post('store', 'store')->name('calendar.store');
       Route::post('destroy', 'destroy')->name('calendar.destroy');
    });
});

Route::middleware(['auth', 'role:admin'])->group(function() {

    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::prefix('users/')->group(function () {
        Route::controller(AdminController::class)->group(function() {
            Route::post('save', 'saveUser')->name('users.save');
            Route::post('{id}/update', 'updateUser')->name('users.update');
        });
    });

});

Route::middleware(['auth', 'role:admin,hr,accounting'])->group(function() {

    Route::get('profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('profile/update', [AdminController::class, 'updateProfile'])->name('profile-update');
    Route::post('profile/update-password', [AdminController::class, 'updateProfilePassword'])->name('profile-password-update');

});

Route::middleware(['auth', 'role:admin,accounting'])->group(function() {
    // Payroll
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll');
    Route::prefix('payroll/')->group(function () {
        Route::controller(PayrollController::class)->group(function() {
            Route::get('create', 'create')->name('payroll.create');
            Route::post('create', 'save')->name('payroll.save');
            Route::get('{id}/view', 'view')->name('payroll.view');
            Route::get('{id}/view/{item_id}/item', 'itemView')->name('payroll.item');

            Route::get('allowances', 'allowances')->name('payroll.allowances');
            Route::post('allowances', 'saveAllowance')->name('payroll.allowances.save');
            Route::post('allowances/{id}/update', 'updateAllowance')->name('payroll.allowances.update');
            Route::delete('allowances/{id}/delete', 'deleteAllowance')->name('payroll.allowances.delete');

            Route::get('deductions', 'deductions')->name('payroll.deductions');
            Route::post('deductions', 'saveDeduction')->name('payroll.deductions.save');
            Route::post('deductions/{id}/update', 'updateDeduction')->name('payroll.deductions.update');
            Route::delete('deductions/{id}/delete', 'deleteDeduction')->name('payroll.deductions.delete');

            Route::post('employee/save-allowance', 'saveEmployeeAllowance')->name('payroll.employee.allowance.save');
            Route::delete('employee/allowance/{allowance_id}/delete', 'deleteEmployeeAllowance')->name('payroll.employee.allowance.delete');
            Route::post('employee/save-deduction', 'saveEmployeeDeduction')->name('payroll.employee.deduction.save');
            Route::delete('employee/deduction/{deduction_id}/delete', 'deleteEmployeeDeduction')->name('payroll.employee.deduction.delete');

            Route::post('generate/report', 'generatePayrollReport')->name('payroll.generate-report');
            Route::post('generate/payslip', 'generatePayslipReport')->name('payroll.generate-payslip');
            Route::post('{id}/regenerate', 'regeneratePayroll')->name('payroll.regenerate');
        });
    });

});

Route::get('employee/login', [EmployeeAuthController::class, 'index'])->name('employee.login');
Route::post('employee/login', [EmployeeAuthController::class, 'login'])->name('employee.login.verify');
Route::get('employee/forgot-password', [EmployeeAuthController::class, 'forgotPassword'])->name('employee.forgot-password');
Route::get('employee/reset-password/{token}', [EmployeeAuthController::class, 'resetPassword'])->name('employee.reset-password');
Route::post('employee/save-reset-password', [EmployeeAuthController::class, 'saveResetPassword'])->name('employee.reset-password.save');
Route::post('employee/email-password', [EmployeeAuthController::class, 'emailPassword'])->name('employee.email-password');
Route::middleware('auth:employee')->group(function() {
    Route::get('employee/home', [EmployeeController::class, 'index'])->name('employee.home');
    Route::get('employee/dtr', [EmployeeController::class, 'dtr'])->name('employee.dtr');
    Route::get('employee/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
    Route::post('employee/password-update', [EmployeeController::class, 'updatePassword'])->name('employee.password-update');
    Route::post('employee/logout', [EmployeeAuthController::class, 'logout'])->name('employee.logout');
});

require __DIR__.'/auth.php';