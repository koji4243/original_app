<?php

namespace App\Listeners;

use App\Events\Reservationed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOfQueueMail;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;
use App\Jobs\Job;

class SendQueueMail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Reservationed $event): void
    {
        dispatch(new Job($event->reservation->id));
    }
}
