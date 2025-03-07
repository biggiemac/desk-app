<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $reservations = Reservation::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', 'confirmed')
            ->get();

        // Add debugging
        \Log::info('Home Controller Reservations:', [
            'count' => $reservations->count(),
            'data' => $reservations->toArray()
        ]);

        $availableSlots = $this->generateWeeklySlots($startOfWeek, $reservations);

        // Add debugging
        \Log::info('Generated Slots:', [
            'slots' => $availableSlots
        ]);

        return view('home', [
            'startDate' => $startOfWeek,
            'slots' => $availableSlots,
        ]);
    }

    private function generateWeeklySlots($startDate, $reservations)
    {
        $slots = [];
        $now = Carbon::now();
        
        for ($i = 0; $i < 5; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $isToday = $currentDate->isToday();
            $isPastDate = $currentDate->isBefore($now->startOfDay());
            
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
                'AM' => !$isPastDate && !$isAMPassed && !$confirmedAM,
                'PM' => !$isPastDate && !$isPMPassed && !$confirmedPM,
                'isPast' => $isPastDate,
                'isAMPassed' => $isAMPassed,
                'isPMPassed' => $isPMPassed
            ];
        }
        return $slots;
    }
}