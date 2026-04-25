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
use App\Mail\SendChangeTitle;
use App\Models\Reservation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservationJob;
    /**
     * Create a new job instance.
     */
    public function __construct($reservation)
    {
        $this->reservationJob = $reservation;
    }

    /**
     * Execute the job.
     */public function handle(): void
    {
        $reservation = $this->reservationJob;
        $user = $reservation->user;

        $response = Http::get(
            config('services.nhk.base'),
            [
                'service' => 'g1',
                'area' => $reservation->nhk_code,
                'date' => Carbon::parse($reservation->start_time)->format('Y-m-d'),
                'key' => config('services.nhk.key'),
            ]);

        $programs = data_get($response->json(), 'g1.publication', []);
        $programMap = collect($programs)
            ->groupBy(fn ($p) => data_get($p, 'identifierGroup.tvSeriesId'));
        // 番組をIDで引けるようにする
        $programsForId = $programMap->get($reservation->nhk_tvEpisodeId);

        if (!$programsForId) return;

        if($reservation->notify_at === null){
            foreach ($programsForId as $program) {
                $apiTime = Carbon::parse(data_get($program, 'startDate'))->format('H:i');
                $resTime = Carbon::parse($reservation->start_time)->format('H:i');

                if ($apiTime === $resTime) {
                    $apiSeriesId = data_get($program, 'identifierGroup.tvSeriesId');
                    //  同じ番組、かつ同じ時間
                    if ($apiSeriesId === $reservation->nhk_tvEpisodeId) {
                        Mail::to($user->email)
                            ->send(new SendOfQueueMail($user, $reservation));
                        $reservation->update(['notify_at' => now()]);
                    //  差し替え
                    } else {
                        Mail::to($user->email)
                            ->send(new SendChangeTitle($user, $reservation));
                        $reservation->update(['notify_at' => now()]);
                    }
                break;
                }
            }
        }
    }
}
