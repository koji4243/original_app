<?php

namespace App\Console\Commands;

use App\Mail\SendMail;
use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SchedulerMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(){
        Log::info('scheduler start', ['time' => now()]);
        $now = now()->floorMinute(); 

        $reservations = Reservation::with('user')
            ->whereNotNull('notify_before_min')
            ->whereNull('notify_at')
            ->whereRaw(
                'DATE_FORMAT(DATE_SUB(start_time, INTERVAL notify_before_min MINUTE), "%Y-%m-%d %H:%i") BETWEEN ? AND ?',
                [$now->copy()->subMinutes(10)->format('Y-m-d H:i'), $now->format('Y-m-d H:i')]
            )
            ->get();
            Log::info($reservations->toArray());

        foreach ($reservations as $reservation) {
            $user = $reservation->user;

            if (!$user || !$user->email) {
                continue;
            }
            Log::info('sending mail', [
                'reservation_id' => $reservation->id,
                'user_id' => $user->id,
            ]);
            // メール送信（同期）
            Mail::to($user->email)
                ->send(new SendMail($user, $reservation));
            $reservation->update([
                'notify_at' => now(),
            ]);
        }
    return 0;
    }
}
