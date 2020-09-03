<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use stdClass;

class SignedUp
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var stdClass  */
    public $domain;

    /** @var stdClass  */
    public $partner;

    /** @var User  */
    public $user;

    /**
     * SignedUp constructor.
     *
     * @param User          $user
     * @param stdClass|null $partner
     * @param stdClass|null $domain
     */
    public function __construct(User $user, stdClass $partner = null, stdClass $domain = null)
    {
        $this->domain = $domain;
        $this->partner = $partner;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
