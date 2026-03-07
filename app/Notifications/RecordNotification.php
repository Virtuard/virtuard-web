<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecordNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->data['id'] ?? uniqid(),
            'event' => 'RecordBroken',
            'to' => 'user',
            'name' => $this->data['name'] ?? 'System',
            'avatar' => $this->data['avatar'] ?? '',
            'link' => $this->data['link'] ?? '',
            'type' => 'record',
            'message' => $this->data['message'] ?? 'Someone broke your record!',
            'puzzle_id' => $this->data['puzzle_id'] ?? null,
            'time_spent' => $this->data['time_spent'] ?? null,
            'moves' => $this->data['moves'] ?? null,
        ];
    }
}
