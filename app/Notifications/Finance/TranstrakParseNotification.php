<?php

namespace App\Notifications\Finance;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TranstrakParseNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    /** @var array  */
    public $json;
    
    /**
     * TranstrakParseNotification constructor.
     *
     * @param array $transtrakJson
     */
    public function __construct(array $transtrakJson)
    {
        $this->json = $transtrakJson;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (!empty($this->json['status']) && $this->json['status'] === 'error') {
            return $this->failureMailMessage($notifiable);
        }
        return (new MailMessage)
                    ->from('noreply@dorcas.ng', 'Transtrak Notification')
                    ->subject('Transtrak processing completed.')
                    ->greeting('Hi ' . $notifiable->firstname)
                    ->line(
                        'Transtrak found ' . number_format($this->json['messages_count']) . ' messages in your INBOX, of '.
                        'which ' . number_format(count($this->json['transactions'] ?? [])) . ' were found, and '.
                        number_format($this->json['saved'] ?? 0) . ' were saved for addition to your finance entries.'
                    )
                    ->line('Thank you for using Transtrak!')
                    ->salutation('- Transtrak');
    }
    
    protected function failureMailMessage($notifiable)
    {
        return (new MailMessage)
                    ->from('noreply@dorcas.ng', 'Transtrak Notification')
                    ->subject('Transtrak processing failed.')
                    ->greeting('Hi ' . $notifiable->firstname)
                    ->line(
                        'Something went wrong while Transtrak was processing your request. Please contact support '.
                        'and inform them of this error: '
                    )
                    ->error()
                    ->line($this->json['reason'])
                    ->line('Thank you for using Transtrak!')
                    ->salutation('- Transtrak');
    }
}
