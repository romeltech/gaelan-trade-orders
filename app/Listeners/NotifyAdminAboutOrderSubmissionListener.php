<?php

namespace App\Listeners;

use App\Mail\OrderSubmissionMail;
use App\Events\OrderSubmissionEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdminAboutOrderSubmissionListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderSubmissionEvent $event)
    {
        Mail::to($event->recipients->pluck('email'))->send(new OrderSubmissionMail($event->order));
        // Mail::to($event->toEmail)->queue(new OrderSubmissionMail($event->order));
    }
}
