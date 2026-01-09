<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Shift;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        
        $date = Carbon::createFromDate($year, $month, 1);
        
        // Get holidays for this month
        $holidays = Holiday::whereYear('date', $year)
                           ->whereMonth('date', $month)
                           ->get()
                           ->keyBy('date');

        $holidayTypes = Shift::where('is_holiday', true)->pluck('name', 'name')->toArray();
        // Fallback if none are defined, or merge? User asked to "fetch all is_holiday data".
        if (empty($holidayTypes)) {
             $holidayTypes = ['Regular Holiday' => 'Regular Holiday', 'Special Holiday' => 'Special Holiday'];
        }

        return view('calendar.index', compact('date', 'holidays', 'holidayTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'type' => 'required|string'
        ]);

        Holiday::updateOrCreate(
            ['date' => $request->date],
            [
                'description' => $request->description,
                'type' => $request->type
            ]
        );

        return back()->with('status', 'Holiday saved successfully.');
    }

    public function destroy(Request $request) 
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        Holiday::where('date', $request->date)->delete();

        return back()->with('status', 'Date reverted to regular day.');
    }
}
