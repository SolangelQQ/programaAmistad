<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->data['title'] ?? 'Nueva notificación')
                    ->line($this->data['message'] ?? 'Tienes una nueva notificación')
                    ->action('Ver notificación', url('/notifications'))
                    ->line('Gracias por usar nuestra aplicación.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->data['title'] ?? 'Notificación',
            'message' => $this->data['message'] ?? 'Nueva notificación',
            'type' => $this->data['type'] ?? 'info',
            'url' => $this->data['url'] ?? null,
            'data' => $this->data['data'] ?? null,
        ];
    }
}