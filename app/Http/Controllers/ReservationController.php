<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yasumi\Yasumi;
use App\Notifications\ReservationConfirmed;
use App\Notifications\ReservationCancelled;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|in:AM,PM',
        ]);

        $now = Carbon::now();
        $bookingDate = Carbon::parse($request->date);
        $isToday = $bookingDate->isToday();

        // Check if booking date is a holiday
        $holidays = Yasumi::create('USA', $bookingDate->year);
        if ($holidays->isHoliday($bookingDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Bookings are not available on holidays.'
            ], 422);
        }

        // Prevent booking past slots
        if ($bookingDate->isBefore($now->startOfDay()) || 
            ($isToday && $request->time_slot === 'AM' && $now->hour >= 12) ||
            ($isToday && $request->time_slot === 'PM' && $now->hour >= 17)) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is no longer available.'
            ], 422);
        }

        // Check if slot is already booked
        $existingReservation = Reservation::where('date', $request->date)
            ->where('time_slot', $request->time_slot)
            ->where('status', 'confirmed')
            ->exists();

        if ($existingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is already booked.'
            ], 422);
        }

        // Check if user already has a booking for this date
        $userHasBooking = Reservation::where('date', $request->date)
            ->where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->exists();

        if ($userHasBooking) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a booking for this date.'
            ], 422);
        }

        // Create the reservation
        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'status' => 'confirmed',
        ]);

        // Send confirmation notification
        auth()->user()->notify(new ReservationConfirmed($reservation));

        return response()->json([
            'success' => true,
            'message' => 'Reservation booked successfully!'
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        // Verify ownership
        if ($reservation->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Store user before cancelling
        $user = $reservation->user;
        
        $reservation->update(['status' => 'cancelled']);

        // Send cancellation notification
        $user->notify(new ReservationCancelled($reservation));

        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled successfully!'
        ]);
    }
}