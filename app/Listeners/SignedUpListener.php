<?php

namespace App\Listeners;

use App\Events\SignedUp;
use App\Notifications\WelcomeEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SignedUpListener implements ShouldQueue
{
    use InteractsWithQueue;

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
     * @param  SignedUp  $event
     * @return void
     */
    public function handle(SignedUp $event)
    {
        $event->user->notify(new WelcomeEmail($event->partner, $event->domain));
        # send a welcome email notification to the user
    }
}
