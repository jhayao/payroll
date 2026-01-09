<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use Intervention\Image\Laravel\Facades\Image;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Shift;
use App\Models\EmployeeShift;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\EmployeeDeduction;
use App\Models\User;

use App\Http\Requests\ProfileUpdateRequest;

use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends Controller
{
    // Home Page
    public function index()
    {
        return view('home', [
            'employeesCount' => Employee::count(),
            'departmentsCount' => Department::count(),
            'positionsCount' => Position::count(),
            'shiftsCount' => Shift::count(),
            'recentEmployees' => Employee::orderBy('id', 'desc')->take(5)->get(),
            'employeesPerDept' => Department::withCount('employees')->get(),
            'employeesPerPosition' => Position::withCount('employees')->get(),
        ]);
    }

    // Employees Records
    public function employees() 
    {
        $employees = Employee::with(['position', 'department'])
            ->orderBy('lastname', 'ASC')
            ->orderby('firstname', 'ASC')
            ->get();

        return view('employees.index', compact('employees'));
    }

    public function createEmployee()
    {
        $positions = Position::pluck('description', 'id');
        $departments = Department::pluck('name', 'id');
        return view('employees.create', compact('positions', 'departments'));
    }

    public function uploadPhoto(Request $request, $employee_id, $photoName)
    {
        $image = $request->file($photoName);
        $ext = $image->getClientOriginalExtension();
        $name = $photoName.'.'.$ext;
        $destinationPath = public_path("/images/uploads/$employee_id");
        $image->move($destinationPath, $name);
        return "/images/uploads/$employee_id/".$name;
    }

    public function saveEmployee(Request $request)
    {
        $validated = $request->validate([
            'lastname' => ['required', 'min:2', 'string'],
            'firstname' => ['required', 'min:2', 'string'],
            'middlename' => ['min:2', 'nullable', 'string'],
            'suffix' => ['nullable', 'string'],
            'address' => ['required', 'min:10', 'string'],
            'mobile_no' => [
                'required',
                'regex:/^(09|\+639)\d{9}$/',
                Rule::unique(Employee::class, 'mobile_no')
            ],
            'sex' => ['string'],
            'position' => ['integer', 'nullable'],
            'department' => ['integer', 'nullable'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Employee::class, 'email')
            ],
            'password' => ['required', Rules\Password::defaults()],
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg|max:2048'],
            'photo2' => ['required', 'image', 'mimes:jpeg,png,jpg|max:2048'],
            'photo3' => ['required', 'image', 'mimes:jpeg,png,jpg|max:2048'],
        ], [
            'mobile_no.regex' => 'Contact number must start with 09 or +639 followed by 9 digits.',
            'photo.required' => 'The photo is required for face recognition purposes.',
            'photo.image' => 'The photo must be an image with the type jpeg or png or jpg.',
            'photo2.required' => 'The photo is required for face recognition purposes.',
            'photo2.image' => 'The photo must be an image with the type jpeg or png or jpg.',
            'photo3.required' => 'The photo is required for face recognition purposes.',
            'photo3.image' => 'The photo must be an image with the type jpeg or png or jpg.',
            'mobile_no.unique' => 'Contact number is already exist.'
        ]);

        $validated['lastname']  = Str::title($validated['lastname']);
        $validated['firstname'] = Str::title($validated['firstname']);
        $validated['middlename'] = Str::title($validated['middlename']);
        $validated['suffix'] = Str::title($validated['suffix']);
        $validated['address']   = Str::title($validated['address']);
        
        $employee_id = time();

        $employee = new Employee($validated);
        $employee->id = $employee_id;
        // handle password hashing
        $employee->password = Hash::make($validated['password']);
        $employee->position_id = $request->position;
        $employee->department_id = $request->department;

        $photo1 = $this->uploadPhoto($request, $employee->id, 'photo');
        $employee->photo_lg = $photo1;

        $photo2 = $this->uploadPhoto($request, $employee->id, 'photo2');
        $employee->photo_lg2 = $photo2;

        $photo3 = $this->uploadPhoto($request, $employee->id, 'photo3');
        $employee->photo_lg3 = $photo3;

        $image = $request->file('photo');
        $title = $employee->id;
        $ext = $image->getClientOriginalExtension();
        $destinationPath = public_path('/images/uploads/');

        Image::read($destinationPath.'/'.$employee->id.'/'.'photo.'.$ext)
            ->cover(600, 600, position: 'center')
            ->save($destinationPath."/2x2/$title.$ext", 100);

        $employee->photo_2x2 = '/images/uploads/2x2/'.$title.'.'.$ext;
        $employee->photo_lg = $photo1;
        $employee->photo_lg2 = $photo3;
        $employee->photo_lg3 = $photo3;

        $employee->save();

        $shift = Shift::where('name', 'Regular Shift')->first();
        EmployeeShift::create([
            'employee_id' => $employee_id,
            'shift_id' => $shift->id,
            'remarks' => 'active'
        ]);
        
        return redirect()->back()->with('status', 'Employee information saved successfully!');
    }

    public function viewEmployee(int $id) 
    {
        $employee = Employee::findOrFail($id);
        $positions = Position::pluck('description', 'id');
        $departments = Department::pluck('name', 'id');
        $shifts = Shift::all();
        return view('employees.view', compact('employee', 'positions', 'departments', 'shifts'));
    }

    public function updateEmployee(Request $request, $id)
    {
        $validated = $request->validate([
            'lastname' => ['required', 'min:2', 'string'],
            'firstname' => ['required', 'min:2', 'string'],
            'middlename' => ['min:2', 'nullable', 'string'],
            'suffix' => ['nullable', 'string'],
            'address' => ['required', 'min:10', 'string'],
            'mobile_no' => [
                'required',
                'regex:/^(09|\+639)\d{9}$/',
                Rule::unique(Employee::class)->ignore($id)
            ],
            'sex' => ['string'],
            'position' => ['integer', 'nullable'],
            'department' => ['integer', 'nullable'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Employee::class)->ignore($id)
            ],
        ], [
            'mobile_no.regex' => 'Contact number must start with 09 or +639 followed by 9 digits.',
            'mobile_no.unique' => 'Contact number is already exist.'
        ]);

        $validated['lastname']  = Str::title($validated['lastname']);
        $validated['firstname'] = Str::title($validated['firstname']);
        $validated['middlename'] = Str::title($validated['middlename']);
        $validated['suffix'] = Str::title($validated['suffix']);
        $validated['address']   = Str::title($validated['address']);
   
        $employee = Employee::findOrFail($id);
        $employee->position_id = $request->position;
        $employee->department_id = $request->department;
        $employee->fill($validated);
        $employee->save();
        return redirect()->back()->with('status', 'Employee information updated successfully!');
    }

    public function updatePhotos(Request $request, $id)
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg|max:2048'],
            'photo2' => ['required', 'image', 'mimes:jpeg,png,jpg|max:2048'],
            'photo3' => ['required', 'image', 'mimes:jpeg,png,jpg|max:2048'],
        ], [
            'photo.required' => 'The photo is required for face recognition purposes.',
            'photo.image' => 'The photo must be an image with the type jpeg or png or jpg.',
            'photo2.required' => 'The photo is required for face recognition purposes.',
            'photo2.image' => 'The photo must be an image with the type jpeg or png or jpg.',
            'photo3.required' => 'The photo is required for face recognition purposes.',
            'photo3.image' => 'The photo must be an image with the type jpeg or png or jpg.'
        ]);

        $employee = Employee::findOrFail($id);

        @unlink(public_path($employee->photo_lg));
        @unlink(public_path($employee->photo_lg2));
        @unlink(public_path($employee->photo_lg3));
        @unlink(public_path($employee->photo_2x2));

        $photo1 = $this->uploadPhoto($request, $employee->id, 'photo');
        $employee->photo_lg = $photo1;

        $photo2 = $this->uploadPhoto($request, $employee->id, 'photo2');
        $employee->photo_lg2 = $photo2;

        $photo3 = $this->uploadPhoto($request, $employee->id, 'photo3');
        $employee->photo_lg3 = $photo3;

        $image = $request->file('photo');
        $title = $employee->id;
        $ext = $image->getClientOriginalExtension();
        $destinationPath = public_path('/images/uploads/');

        Image::read($destinationPath.'/'.$employee->id.'/'.'photo.'.$ext)
            ->cover(600, 600, position: 'center')
            ->save($destinationPath."/2x2/$title.$ext", 100);

        $employee->photo_2x2 = '/images/uploads/2x2/'.$title.'.'.$ext;
        $employee->photo_lg = $photo1;
        $employee->photo_lg2 = $photo3;
        $employee->photo_lg3 = $photo3;

        $employee->save();
        return redirect()->back()->with('status', 'Photo updated successfully!');
    }

    public function updatePassword(Request $request, $id)
    {
        $validated = $request->validate([
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $employee = Employee::findOrFail($id);
        $employee->password = Hash::make($validated['password']);
        $employee->save();
        return redirect()->back()->with('status', 'Password updated successfully!');
    }

    public function updateEmployeeShift(Request $request, $id)
    {
        $shift = EmployeeShift::find($id);
        $shift->shift_id = $request->shift_id;
        $shift->save();
        return redirect()->route('employees.view', $shift->employee_id)->with('status', 'Official time updated.');
    }

    // Departments
    public function departments()
    {
        $departments = Department::all();
        return view('departments.index', compact('departments'));
    }

    public function addDepartment() 
    {
        return view('departments.create');
    }

    public function saveDepartment(Request $request) 
    {
        $request->validate([
            'name' => ['required', 'min:2', 'unique:departments,name'],
            'abbr' => ['required', 'min:2']
        ], [
            'abbr.required' => 'The abbreviation is riquired.', 
            'abbr.min' => 'The abbreviation should at least 2 characters.' 
        ]);

        Department::create([
            'name' => $request->name,
            'abbr' => $request->abbr
        ]);

        return redirect()->route('departments')->with('status', 'Department created successfully.');
    }

    public function editDepartment($id) 
    {
        $department = Department::find($id);
        return view('departments.edit', compact('department'));
    }

    public function updateDepartment(Request $request, $id) 
    {
        $request->validate([
            'name' => [
                'required', 'min:2', 
                Rule::unique('departments', 'name')->ignore($id)
            ],
            'abbr' => ['required', 'min:2']
        ], [
            'abbr.required' => 'The abbreviation is riquired.', 
            'abbr.min' => 'The abbreviation should at least 2 characters.' 
        ]);

        $department = Department::find($id);
        $department->update([
            'name' => $request->name,
            'abbr' => $request->abbr
        ]);

        return redirect()->route('departments')->with('status', 'Department updated successfully.');
    }

    public function deleteDepartment(Request $request, $id) 
    {
        $department = Department::find($id);
        $department->delete();
        return redirect()->route('departments')->with('status', 'Department deleted successfully.');
    }

    // Positions
    public function positions()
    {
        $positions = Position::withCount('employees')->get();
        return view('positions.index', compact('positions'));
    }

    public function addPosition() 
    {
        return view('positions.create');
    }

    public function savePosition(Request $request) 
    {
        $request->validate([
            'description' => ['required', 'min:2', 'unique:positions,description'],
            'daily_rate' => ['required', 'numeric'],
            'hourly_rate' => ['required', 'numeric'],
            'minutely_rate' => ['required', 'numeric'],
            'holiday_rate' => ['required', 'numeric'],
        ], [
            'daily_rate.required' => 'The daily rate is riquired.', 
            'daily_rate.numeric' => 'The daily rate must be numeric.',

            'hourly_rate.required' => 'The hourly rate is riquired.', 
            'hourly_rate.numeric' => 'The hourly rate must be numeric.',

            'minutely_rate.required' => 'The minutely rate is riquired.', 
            'minutely_rate.numeric' => 'The minutely rate must be numeric.',

            'holiday_rate.required' => 'The holiday rate is riquired.', 
            'holiday_rate.numeric' => 'The holiday rate must be numeric.',
            
        ]);

        Position::create([
            'description' => $request->description,
            'daily_rate' => $request->daily_rate,
            'hourly_rate' => $request->hourly_rate,
            'minutely_rate' => $request->minutely_rate,
            'holiday_rate' => $request->holiday_rate
        ]);

        return redirect()->route('positions')->with('status', 'Position created successfully.');
    }

    public function editPosition($id) 
    {
        $position = Position::find($id);
        return view('positions.edit', compact('position'));
    }

    public function updatePosition(Request $request, $id) 
    {
        $request->validate([
            'description' => [
                'required', 'min:2',
                Rule::unique('positions', 'description')->ignore($id)
            ],
            'daily_rate' => ['required', 'numeric'],
            'hourly_rate' => ['required', 'numeric'],
            'minutely_rate' => ['required', 'numeric'],
            'holiday_rate' => ['required', 'numeric'],
        ], [
            'daily_rate.required' => 'The daily rate is riquired.', 
            'daily_rate.numeric' => 'The daily rate must be numeric.',

            'hourly_rate.required' => 'The hourly rate is riquired.', 
            'hourly_rate.numeric' => 'The hourly rate must be numeric.',

            'minutely_rate.required' => 'The minutely rate is riquired.', 
            'minutely_rate.numeric' => 'The minutely rate must be numeric.',

            'holiday_rate.required' => 'The holiday rate is riquired.', 
            'holiday_rate.numeric' => 'The holiday rate must be numeric.',
            
        ]);

        $position = Position::find($id); // or ->findOrFail($id)
        $position->update([
            'description' => $request->description,
            'daily_rate' => $request->daily_rate,
            'hourly_rate' => $request->hourly_rate,
            'minutely_rate' => $request->minutely_rate,
            'holiday_rate' => $request->holiday_rate
        ]);

        return redirect()->route('positions')->with('status', 'Position updated successfully.');
    }

    public function deletePosition(Request $request, $id) 
    {
        $position = Position::find($id);
        $position->delete();
        return redirect()->route('positions')->with('status', 'Position deleted successfully.');
    }

    // Shifts
    public function shifts()
    {
        $shifts = Shift::all();
        return view('shifts.index', compact('shifts'));
    }

    public function addShift() 
    {
        return view('shifts.create');
    }

    public function saveShift(Request $request) 
    {
        $request->validate([
            'name' => ['required', 'min:2', 'unique:departments,name'],
            'am_in' => [
                'required', 
                'regex: /^([01]\d|2[0-3]):[0-5]\d$/'
            ],
            'am_out' => [
                'required', 
                'regex: /^([01]\d|2[0-3]):[0-5]\d$/'
            ],
            'pm_in' => [
                'required', 
                'regex: /^([01]\d|2[0-3]):[0-5]\d$/'
            ],
            'pm_out' => [
                'required', 
                'regex: /^([01]\d|2[0-3]):[0-5]\d$/'
            ],
            /*
            'in_out_interval' => [
                'required', 
                'integer',
                'min:1'
            ],
            'out_in_interval' => [
                'required', 
                'integer',
                'min:1'
            ],
            */
        ], [
            'am_in.required' => 'The is riquired.', 
            'am_in.regex' => 'The format must be like 08:00',

            'am_out.required' => 'The is riquired.', 
            'am_out.regex' => 'The format must be like 08:00',

            'pm_in.required' => 'The is riquired.', 
            'pm_in.regex' => 'The format must be like 08:00',

            'pm_out.required' => 'The is riquired.', 
            'pm_out.regex' => 'The format must be like 08:00',
            
            /*
            'in_out_interval.required' => 'The is riquired.', 
            'in_out_interval.integer' => 'Interval must be integer.',
            'in_out_interval.min' => 'Minimum interval is 1.',

            'out_in_interval.required' => 'The is riquired.', 
            'out_in_interval.integer' => 'Interval must be integer.',
            'out_in_interval.min' => 'Minimum interval is 1.',
            */
        ]);

        Shift::create([
            'name' => $request->name,
            'am_in' => $request->am_in,
            'am_out' => $request->am_out,
            'pm_in' => $request->pm_in,
            'pm_out' => $request->pm_out,
            //'in_out_interval' => $request->in_out_interval,
            //'out_in_interval' => $request->out_in_interval,
        ]);

        return redirect()->route('shifts')->with('status', 'Shift created successfully.');
    }

    public function editShift($id) 
    {
        $shift = Shift::find($id);
        return view('shifts.edit', compact('shift'));
    }

    public function updateShift(Request $request, $id) 
    {
        $request->validate([
            'name' => [
                'required', 'min:2', 
                Rule::unique('shifts', 'name')->ignore($id)
            ],
            'am_in' => [
                'required', 
                'regex: /^([01]\d|2[0-3]):[0-5]\d$/'
            ],
            'am_out' => [
                'required', 
                'regex: /^([01]\d|2[0-3]):[0-5]\d$/'
            ],
            'pm_in' => [
                'required', 
                'regex: /^([01]\d|2[0-3]):[0-5]\d$/'
            ],
            'pm_out' => [
                'required', 
                'regex: /^([01]\d|2[0-3]):[0-5]\d$/'
            ],
            /*
            'in_out_interval' => [
                'required', 
                'integer',
                'min:1'
            ],
            'out_in_interval' => [
                'required', 
                'integer',
                'min:1'
            ],
            */
        ], [
            'am_in.required' => 'The is riquired.', 
            'am_in.regex' => 'The format must be like 08:00',

            'am_out.required' => 'The is riquired.', 
            'am_out.regex' => 'The format must be like 08:00',

            'pm_in.required' => 'The is riquired.', 
            'pm_in.regex' => 'The format must be like 08:00',

            'pm_out.required' => 'The is riquired.', 
            'pm_out.regex' => 'The format must be like 08:00',

            /*
            'in_out_interval.required' => 'The is riquired.', 
            'in_out_interval.integer' => 'Interval must be integer.',
            'in_out_interval.min' => 'Minimum interval is 1.',

            'out_in_interval.required' => 'The is riquired.', 
            'out_in_interval.integer' => 'Interval must be integer.',
            'out_in_interval.min' => 'Minimum interval is 1.',
            */
        ]);

        $shift = Shift::find($id);
        $shift->update([
            'name' => $request->name,
            'am_in' => $request->am_in,
            'am_out' => $request->am_out,
            'pm_in' => $request->pm_in,
            'pm_out' => $request->pm_out,
            //'in_out_interval' => $request->in_out_interval,
            //'out_in_interval' => $request->out_in_interval,
        ]);

        return redirect()->route('shifts')->with('status', 'Shift updated successfully.');
    }

    public function deleteShift(Request $request, $id) 
    {
        $shift = Shift::find($id);
        $shift->delete();
        return redirect()->route('shifts')->with('status', 'Shift deleted successfully.');
    }

    public function profile()
    {
        return view('profile');
    }

    public function updateProfile(ProfileUpdateRequest $request) 
    {
        $request->user()->fill($request->validated());
        $request->user()->save();
        return back()->with('status', 'Profile updated successfully.');
    }

    public function updateProfilePassword(Request $request) 
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('status', 'Password changed successfully.');
    }

    public function dtr() {
        $employees = Employee::orderBy('lastname', 'asc')
                            ->orderBy('firstname', 'asc')
                            ->get();
        return view('dtr.index', compact('employees'));
    }

    public function generateDTRReport(Request $request)
    {
        $request->validate([
            'employee_id' => ['required'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from']
        ], [
            'employee_id.reuired' => 'Please select employee.',
            'date_from.required' => 'Please specify date from.',
            'date_from.date' => 'Date from should be date.',
            'date_to.required' => 'Please specify date to.',
            'date_to.date' => 'Date to should be date.',
            'date_to.date' => 'Date to should after or equal to date from.',
        ]);

        $id = $request->employee_id;
        $from = $request->date_from;
        $to = $request->date_to;

        $employee = Employee::find($id);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('dtr.print', compact('employee', 'from', 'to'))->render());
        $dompdf->setPaper('folio', 'portrait');
        $dompdf->render();

        return $dompdf->stream($employee->lastname . '--' . $from . '--'. $to . '.pdf');
    }

    public function users() 
    {
        $users = User::where('id', '!=', auth()->user()->id)
                     ->where('role', '!=', 'admin')
                     ->get();
        return view('users.index', compact('users'));
    }

    public function saveUser(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => Str::title($request->name),
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('users')->with('status', 'User created successfully.');
    }

    public function updateUser(Request $request, $id) 
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($id)
            ]
        ]);

        $user = User::findOrFail($id);
        $user->name = Str::title($request->name);
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();
        return redirect()->back()->with('status', 'User updated successfully.');
    }
}
