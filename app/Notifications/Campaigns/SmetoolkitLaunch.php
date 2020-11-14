<?php

namespace App\Notifications\Campaigns;

use App\Mail\Campaigns\SmetoolkitLaunchEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SmetoolkitLaunch extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
     * @param $notifiable
     *
     * @return SmetoolkitLaunchEmail
     */
    public function toMail($notifiable)
    {
        return (new SmetoolkitLaunchEmail($notifiable))->to($notifiable);
    }
}
