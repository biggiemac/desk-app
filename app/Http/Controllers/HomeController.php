<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $weekOffset = (int) $request->query('week', 0);
        
        $startDate = Carbon::now()
            ->addWeeks($weekOffset)
            ->startOfWeek();
        
        $endDate = $startDate->copy()->endOfWeek();

        $reservations = Reservation::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'confirmed')
            ->get();

        $availableSlots = $this->generateWeeklySlots($startDate, $reservations);

        return view('home', compact('availableSlots'));
    }

    private function generateWeeklySlots($startDate, $reservations)
    {
        $slots = [];
        $now = Carbon::now();
        
        for ($i = 0; $i < 5; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            
            // Skip weekend days
            if ($currentDate->isWeekend()) {
                continue;
            }

            $isToday = $currentDate->isToday();
            $isPastDate = $currentDate->isBefore($now->startOfDay());
            
            // Define cutoff times
            $amCutoff = $currentDate->copy()->setHour(12)->setMinute(0);
            $pmCutoff = $currentDate->copy()->setHour(17)->setMinute(0);
            
            $isAMPassed = $isToday && $now->greaterThanOrEqualTo($amCutoff);
            $isPMPassed = $isToday && $now->greaterThanOrEqualTo($pmCutoff);
            
            $dateKey = $currentDate->format('Y-m-d');
            
            $slots[$dateKey] = [
                'AM' => !$isPastDate && !$isAMPassed && !$this->isSlotBooked($reservations, $dateKey, 'AM'),
                'PM' => !$isPastDate && !$isPMPassed && !$this->isSlotBooked($reservations, $dateKey, 'PM')
            ];
        }
        
        return $slots;
    }

    private function isSlotBooked($reservations, $date, $timeSlot)
    {
        return $reservations->contains(function ($reservation) use ($date, $timeSlot) {
            return $reservation->date->format('Y-m-d') === $date 
                && $reservation->time_slot === $timeSlot;
        });
    }
}