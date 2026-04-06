<?php

namespace App\Console\Commands;

use App\Mail\SendMail;
use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Events\Reservationed;
use App\Mail\SendOfQueueMail;


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
        //通知時間抽出 → メール送信
        $now = now()->floorMinute(); 

        $reservations = Reservation::with('user')
            ->whereNotNull('notify_before_min')
            ->whereNull('notify_at')
            ->whereRaw(
                'DATE_FORMAT(DATE_SUB(start_time, INTERVAL notify_before_min MINUTE), "%Y-%m-%d %H:%i") BETWEEN ? AND ?',
                [$now->copy()->subMinutes(10)->format('Y-m-d H:i'), $now->format('Y-m-d H:i')]
            )
            ->get();
        foreach ($reservations as $reservation) {
            $reservation = Reservation::with('user')->find($reservation->id);
            if (!$reservation || !$reservation->user) {
                Log::error('Reservation or user not found', [
                    'reservation_id' => $this->reservationId
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
}
