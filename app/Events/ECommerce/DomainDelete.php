<?php

namespace App\Events\ECommerce;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DomainDelete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    /** @var array  */
    public $domain;
    
    /** @var null|string  */
    public $token;
    
    /**
     * DomainDelete constructor.
     *
     * @param array       $domain
     * @param string|null $authToken
     */
    public function __construct(array $domain, string $authToken = null)
    {
        $this->domain = $domain;
        $this->token = $authToken;
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
