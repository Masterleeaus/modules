<?php

namespace Modules\Biometric\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Biometric\Events\BiometricClockIn;

/**
 * Sent to a supervisor or client when a biometric clock-in is recorded.
 *
 * @property string $recipient  'supervisor' | 'client'
 */
class BiometricClockInNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly BiometricClockIn $event,
        private readonly string $recipient = 'supervisor'
    ) {
    }

    public function via(mixed $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $user     = $this->event->user;
        $time     = $this->event->attendance->clock_in_time?->format('H:i') ?? now()->format('H:i');
        $method   = ucfirst($this->event->method);

        if ($this->recipient === 'client') {
            $subject = "{$user->name} has arrived at your location";
            $line    = "Your cleaner **{$user->name}** clocked in at {$time} via {$method}.";
        } else {
            $subject = "{$user->name} clocked in";
            $line    = "**{$user->name}** clocked in at {$time} via {$method}.";
        }

        return (new MailMessage)
            ->subject($subject)
            ->line($line);
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'user_id'        => $this->event->user->id,
            'user_name'      => $this->event->user->name,
            'attendance_id'  => $this->event->attendance->id,
            'method'         => $this->event->method,
            'geofence_passed'=> $this->event->geofencePassed,
            'clock_in_time'  => $this->event->attendance->clock_in_time?->toIso8601String(),
            'recipient'      => $this->recipient,
        ];
    }
}
