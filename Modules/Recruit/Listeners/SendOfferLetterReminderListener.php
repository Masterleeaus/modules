<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Notifications\SendOfferLetterReminder;
use Notification;

class SendOfferLetterReminderListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Notification::send($event->event->jobApplication, new SendOfferLetterReminder($event));
    }
}
