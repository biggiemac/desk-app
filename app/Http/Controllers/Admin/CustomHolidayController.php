<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomHoliday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomHolidayController extends Controller
{
    public function index()
    {
        $holidays = CustomHoliday::orderBy('date')
            ->get()
            ->groupBy(function($holiday) {
                return $holiday->date->format('Y');
            });

        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string',
        ]);

        CustomHoliday::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Holiday added successfully.');
    }

    public function destroy(CustomHoliday $holiday)
    {
        $holiday->delete();
        return back()->with('success', 'Holiday removed successfully.');
    }
}
