<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yasumi\Yasumi;
use App\Models\CustomHoliday;

class DashboardController extends Controller
{
    public function index()
    {
        $weekOffset = (int) request('week', 0);
        
        $startDate = Carbon::now()
            ->addWeeks($weekOffset)
            ->startOfWeek();
        
        $endDate = $startDate->copy()->endOfWeek();

        $reservations = Reservation::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'confirmed')
            ->get();

        $upcomingReservations = Reservation::where('user_id', auth()->id())
            ->where('date', '>=', now()->startOfDay())
            ->where('status', 'confirmed')
            ->orderBy('date')
            ->get();

        $slots = $this->generateWeeklySlots($startDate, $reservations);

        return view('dashboard', [
            'slots' => $slots,
            'upcomingReservations' => $upcomingReservations,
            'weekOffset' => $weekOffset,
            'startOfWeek' => $startDate,
            'endOfWeek' => $endDate
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
            
            // Skip weekend days
            if ($currentDate->isWeekend()) {
                continue;
            }

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
                    if ($holidays->isHoliday($currentDate)) {
                        $holidayName = $holidays->holiday($currentDate)->getName();
                    } elseif ($nextYearHolidays->isHoliday($currentDate)) {
                        $holidayName = $nextYearHolidays->holiday($currentDate)->getName();
                    }
                }
            }
            
            // Define cutoff times
            $amCutoff = $currentDate->copy()->setHour(12)->setMinute(0);
            $pmCutoff = $currentDate->copy()->setHour(17)->setMinute(0);
            
            $isAMPassed = $isToday && $now->greaterThanOrEqualTo($amCutoff);
            $isPMPassed = $isToday && $now->greaterThanOrEqualTo($pmCutoff);
            
            $dateKey = $currentDate->format('Y-m-d');
            
            $slots[$dateKey] = [
                'AM' => !$isPastDate && !$isAMPassed && !$this->isSlotBooked($reservations, $dateKey, 'AM') && !$isHoliday,
                'PM' => !$isPastDate && !$isPMPassed && !$this->isSlotBooked($reservations, $dateKey, 'PM') && !$isHoliday,
                'date' => $currentDate,
                'isPast' => $isPastDate,
                'isAMPassed' => $isAMPassed,
                'isPMPassed' => $isPMPassed,
                'isHoliday' => $isHoliday,
                'holidayName' => $holidayName
            ];
        }
        
        return $slots;
    }

    private function isSlotBooked($reservations, $date, $timeSlot)
    {
        return $reservations->contains(function ($reservation) use ($date, $timeSlot) {
            return $reservation->date->format('Y-m-d') === $date 
                && $reservation->time_slot === $timeSlot
                && $reservation->status === 'confirmed';
        });
    }
}