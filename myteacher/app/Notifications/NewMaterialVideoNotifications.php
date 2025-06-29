<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMaterialVideoNotifications extends Notification
{
    use Queueable;
    public $chapter_name;
    /**
     * Create a new notification instance.
     */
    public function __construct($chapter_name)
    {
        $this->chapter_name = $chapter_name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' =>  ' تم تحميل فيديو دراسي جديد ضمن ' .$this->chapter_name
        ];
    }
}
