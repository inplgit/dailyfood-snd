<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use App\Events\RequestNotificationEvent;

class RequestNotification extends Notification
{
    use Queueable;
    public $user;
    public $message;
    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user2 , $user , $message , $url)
    {
        $this->to_user = $user2;
        $this->user = $user;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
        // return ['database' , 'broadcast'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // event(new RequestNotificationEvent($this->to_user , $this->message , $this->url));
        broadcast(new RequestNotificationEvent($this->to_user , $this->message , $this->url));

        return [
            'id' => $this->user['id'],
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'message' => $this->message,
            'url' => $this->url
        ];
    }

}
