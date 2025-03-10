<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomHoliday;
use Illuminate\Http\Request;
use Yasumi\Yasumi;
use Carbon\Carbon;

class CustomHolidayController extends Controller
{
    public function index()
    {
        // Get custom holidays
        $customHolidays = CustomHoliday::orderBy('date')
            ->paginate(10);

        // Get US federal holidays for current and next year
        $currentYear = Carbon::now()->year;
        $nextYear = $currentYear + 1;
        
        $currentYearHolidays = Yasumi::create('USA', $currentYear);
        $nextYearHolidays = Yasumi::create('USA', $nextYear);

        $federalHolidays = collect();
        
        // Add current year holidays
        foreach ($currentYearHolidays->getHolidays() as $holiday) {
            $federalHolidays->push([
                'name' => $holiday->getName(),
                'date' => $holiday->format('Y-m-d'),
                'type' => 'Federal'
            ]);
        }

        // Add next year holidays
        foreach ($nextYearHolidays->getHolidays() as $holiday) {
            $federalHolidays->push([
                'name' => $holiday->getName(),
                'date' => $holiday->format('Y-m-d'),
                'type' => 'Federal'
            ]);
        }

        // Sort federal holidays by date
        $federalHolidays = $federalHolidays->sortBy('date');

        return view('admin.holidays.index', compact('customHolidays', 'federalHolidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
        ]);

        CustomHoliday::create([
            'name' => $request->name,
            'date' => $request->date,
        ]);

        return back()->with('success', 'Holiday added successfully.');
    }

    public function destroy(CustomHoliday $holiday)
    {
        $holiday->delete();
        return back()->with('success', 'Holiday removed successfully.');
    }
}
