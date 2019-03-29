<?php

namespace App\Mail;

use App\Models\User;
use Hostville\Dorcas\Sdk;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var \stdClass  */
    public $domain;

    /** @var \stdClass  */
    public $partner;

    /** @var User  */
    public $user;

    /**
     * WelcomeEmail constructor.
     *
     * @param User           $user
     * @param \stdClass|null $partner
     * @param \stdClass|null $domain
     */
    public function __construct(User $user, \stdClass $partner = null, \stdClass $domain = null)
    {
        $this->domain = $domain;
        $this->partner = $partner;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $appUiSettings = [];
        if (!empty($this->partner)) {
            $configuration = (array) $this->partner->extra_data;
            $appUiSettings = $configuration['hubConfig'] ?? [];
            $appUiSettings['product_logo'] = !empty($this->partner->logo) ? $this->partner->logo : null;
        }
        $subject = 'Welcome to ' . ($appUiSettings['product_name'] ?? 'Hub') . ', ' . $this->user->firstname;
        $subdomain = null;
        if (empty($this->domain) && !empty($this->partner->domain_issuances)) {
            $this->domain = $this->partner->domain_issuances['data'][0] ?? null;
        }
        if (!empty($this->domain)) {
            $subdomain = 'https://' . $this->domain->prefix . '.' . $this->domain->domain['data']['domain'];
        }
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->replyTo(config('mail.from.address'), config('mail.from.name'))
                    ->subject($subject)
                    ->view('emails.welcome')
                    ->with(['subdomain' => $subdomain]);
    }
}
