<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendOfQueueMail;
use App\Models\Reservation;


class ReservationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;
    /**
     * Create a new job instance.
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Execute the job.
     */
    public function handle(): void{
        $reservation = $this->reservation;
    
        if (!$reservation || !$reservation->user) {
            Log::error('Reservation or user not found', [
                'reservation_id' => $this->reservation
            ]);
            return;
        }
        Mail::to($reservation->user->email)
            ->send(new SendOfQueueMail($reservation->user, $reservation));

            // notify_at更新
        if (!$reservation->notify_at) {
            $reservation->update(['notify_at' => now()]);
        }
    }
}
