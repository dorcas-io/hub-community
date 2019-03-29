<?php

namespace App\Mail\Campaigns;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SmetoolkitLaunchEmail extends Mailable
{
    use Queueable, SerializesModels;
    
    /** @var string */
    public $notifiable;
    
    /**
     * SmetoolkitLaunchEmail constructor.
     *
     * @param $notifiable
     */
    public function __construct($notifiable)
    {
        $this->notifiable = $notifiable;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Introducing SMEToolkit Hub (Test 3)';
        return $this->from(config('mail.from.address'), 'SMEToolkit')
                    ->replyTo(config('mail.from.address'), config('mail.from.name'))
                    ->subject($subject)
                    ->view('emails.campaigns.smetoolkit-launch');
    }
}
