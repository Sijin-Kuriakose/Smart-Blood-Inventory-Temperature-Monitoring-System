<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CriticalTemperatureNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $alert;
    protected $refrigerator;

    /**
     * Create a new notification instance.
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
        $this->refrigerator = $alert->refrigerator;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Only storing in database for API access
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'refrigerator_id' => $this->refrigerator->id,
            'refrigerator_code' => $this->refrigerator->refrigerator_code,
            'message' => $this->alert->message,
            'triggered_at' => $this->alert->triggered_at->toIso8601String(),
        ];
    }
}
