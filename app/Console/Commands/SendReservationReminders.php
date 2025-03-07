<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Notifications\ReservationReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendReservationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for tomorrow\'s reservations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $this->info("Checking for reservations on: " . $tomorrow);
        
        // First, let's see all reservations to debug
        $allReservations = Reservation::all();
        $this->info("Total reservations in system: " . $allReservations->count());
        foreach ($allReservations as $res) {
            $this->info("Found reservation for: " . $res->date->format('Y-m-d') . " status: " . $res->status);
        }
        
        // Now check for tomorrow's reservations
        $reservations = Reservation::with('user')
            ->whereDate('date', $tomorrow)
            ->where('status', 'confirmed')
            ->get();

        $this->info("Found tomorrow's reservations: " . $reservations->count());
        
        foreach ($reservations as $reservation) {
            $this->info("Sending reminder to: " . $reservation->user->email . " for " . $reservation->date->format('Y-m-d'));
            $reservation->user->notify(new ReservationReminder($reservation));
        }

        $this->info("Sent {$reservations->count()} reservation reminders.");
    }
}
