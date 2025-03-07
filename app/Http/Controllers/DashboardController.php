<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yasumi\Yasumi;
use App\Models\CustomHoliday;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get the week offset from the query string (default to 0 for current week)
        $weekOffset = (int) $request->query('week', 0);
        
        // Calculate start and end dates for the requested week
        $startOfWeek = Carbon::now()->startOfWeek()->addWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->addWeeks($weekOffset);
        
        // Get upcoming reservations for the user
        $upcomingReservations = $request->user()
            ->reservations()
            ->where('date', '>=', now())
            ->where('status', 'confirmed')
            ->orderBy('date')
            ->get();

        // Get all confirmed reservations for the week
        $reservations = Reservation::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', 'confirmed')
            ->get();

        $availableSlots = $this->generateWeeklySlots($startOfWeek, $reservations);

        return view('dashboard', [
            'upcomingReservations' => $upcomingReservations,
            'slots' => $availableSlots,
            'weekOffset' => $weekOffset,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
        ]);
    }

    private function generateWeeklySlots($startDate, $reservations)
    {
        $slots = [];
        $now = Carbon::now();
        
        // Get US holidays
        $holidays = Yasumi::create('USA', $startDate->year);
        $nextYearHolidays = Yasumi::create('USA', $startDate->year + 1);
        
        // Get custom holidays
        $customHolidays = CustomHoliday::whereBetween('date', [
            $startDate->format('Y-m-d'),
            $startDate->copy()->addDays(4)->format('Y-m-d')
        ])->get();
        
        for ($i = 0; $i < 5; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $isToday = $currentDate->isToday();
            $isPastDate = $currentDate->isBefore($now->startOfDay());
            
            // Check if current date is a holiday (federal or custom)
            $isHoliday = $holidays->isHoliday($currentDate) || 
                         $nextYearHolidays->isHoliday($currentDate) ||
                         $customHolidays->contains('date', $currentDate->format('Y-m-d'));
            
            // Get holiday name
            $holidayName = null;
            if ($isHoliday) {
                $customHoliday = $customHolidays->where('date', $currentDate->format('Y-m-d'))->first();
                if ($customHoliday) {
                    $holidayName = $customHoliday->name;
                } else {
                    $holidayName = $holidays->isHoliday($currentDate) 
                        ? $holidays->holiday($currentDate)->getName() 
                        : $nextYearHolidays->holiday($currentDate)->getName();
                }
            }
            
            // Define cutoff times
            $amCutoff = $currentDate->copy()->setHour(12)->setMinute(0);
            $pmCutoff = $currentDate->copy()->setHour(17)->setMinute(0);
            
            // Check if slots are past their cutoff
            $isAMPassed = $isToday && $now->greaterThanOrEqualTo($amCutoff);
            $isPMPassed = $isToday && $now->greaterThanOrEqualTo($pmCutoff);
            
            // Get confirmed reservations for this date
            $confirmedAM = $reservations->filter(function($reservation) use ($currentDate) {
                return $reservation->date->format('Y-m-d') === $currentDate->format('Y-m-d') 
                    && $reservation->time_slot === 'AM' 
                    && $reservation->status === 'confirmed';
            })->isNotEmpty();
                
            $confirmedPM = $reservations->filter(function($reservation) use ($currentDate) {
                return $reservation->date->format('Y-m-d') === $currentDate->format('Y-m-d') 
                    && $reservation->time_slot === 'PM' 
                    && $reservation->status === 'confirmed';
            })->isNotEmpty();
            
            $slots[$currentDate->format('Y-m-d')] = [
                'AM' => !$isPastDate && !$isAMPassed && !$confirmedAM && !$isHoliday,
                'PM' => !$isPastDate && !$isPMPassed && !$confirmedPM && !$isHoliday,
                'isPast' => $isPastDate,
                'isAMPassed' => $isAMPassed,
                'isPMPassed' => $isPMPassed,
                'isHoliday' => $isHoliday,
                'holidayName' => $holidayName
            ];
        }
        return $slots;
    }
}