<?php

namespace App\Notifications;

use App\Mail\WelcomeEmail as Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var \stdClass  */
    public $domain;

    /** @var \stdClass  */
    public $partner;

    /**
     * WelcomeEmail constructor.
     *
     * @param \stdClass|null $partner
     * @param \stdClass|null $domain
     */
    public function __construct(\stdClass $partner = null, \stdClass $domain = null)
    {
        $this->domain = $domain;
        $this->partner = $partner;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return Mailable
     */
    public function toMail($notifiable)
    {
        return (new Mailable($notifiable, $this->partner, $this->domain))->to($notifiable);
    }
}
