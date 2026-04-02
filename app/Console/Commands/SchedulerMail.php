<?php

namespace App\Console\Commands;

use App\Mail\SendMail;
use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Events\Reservationed;

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
         // イベント発火 → キュー
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
            event(new Reservationed($reservation));
        }
    }
}
