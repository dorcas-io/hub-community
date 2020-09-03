<?php

namespace App\Events\Finance;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TranstrakParse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    /** @var array */
    public $json;
    
    /** @var null|string  */
    public $authToken;
    
    /**
     * TranstrakParse constructor.
     *
     * @param array       $payload
     * @param string|null $authorizationToken
     */
    public function __construct(array $payload, string $authorizationToken = null)
    {
        $this->authToken = $authorizationToken;
        $this->json = $payload;
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
