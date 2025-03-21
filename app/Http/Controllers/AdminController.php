<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalReservations = Reservation::where('status', 'confirmed')->count();
        $activeUsers = User::count();
        $weeklyBookings = Reservation::where('status', 'confirmed')
            ->whereBetween('date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count();

        $recentReservations = Reservation::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalReservations',
            'activeUsers',
            'weeklyBookings',
            'recentReservations'
        ));
    }

    public function users()
    {
        $users = User::orderBy('name')
            ->paginate(10);

        return view('admin.users', compact('users'));
    }

    public function reservations()
    {
        $reservations = Reservation::with('user')
            ->orderBy('date', 'desc')
            ->paginate(10);

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