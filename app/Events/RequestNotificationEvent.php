<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $id;
    public $name;
    public $email;
    public $message;
    public $url;


    public function __construct($user , $message , $url)
    {
        $this->id = $user['id'];
        $this->name = $user['name'];
        $this->email = $user['email'];
        $this->message = $message;
        $this->url = $url;
    }

    // public function __construct()
    // {
    //     $this->id = 173;
    //     $this->name = 'adsad';
    //     $this->email = 'asdsadsad';
    //     $this->message = 'asdsad';
    //     $this->url = 'asdsadsad';
    // }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('notification.'.$this->id);
        // return new Channel('notification.'.$this->id);
        // return new Channel('notification');
        return ['notification'];

    }

    public function broadcastAs()
    {
        return 'RequestNotificationEvent';
    }

}
