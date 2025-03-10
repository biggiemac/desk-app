<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalReservations = Reservation::where('status', 'confirmed')->count();
        $todayReservations = Reservation::where('date', today())
            ->where('status', 'confirmed')
            ->count();

        return view('admin.dashboard', compact('totalUsers', 'totalReservations', 'todayReservations'));
    }

    public function users()
    {
        $users = User::withCount(['reservations' => function($query) {
            $query->where('status', 'confirmed');
        }])->paginate(10);

        return view('admin.users', compact('users'));
    }

    public function reservations()
    {
        $reservations = Reservation::with('user')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('admin.reservations', compact('reservations'));
    }

    public function cancelReservation(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservation cancelled successfully.');
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id !== auth()->id()) {
            $user->update(['is_admin' => !$user->is_admin]);
            return back()->with('success', 'User admin status updated successfully.');
        }

        return back()->with('error', 'You cannot modify your own admin status.');
    }
}