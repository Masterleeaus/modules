<?php

namespace Modules\Biometric\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Biometric\Events\BiometricClockIn;

/**
 * Sent to company admins when an employee's GPS is outside the configured
 * geofence radius at clock-in time.
 */
class BiometricGeofenceFailedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly BiometricClockIn $event
    ) {
    }

    public function via(mixed $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $user = $this->event->user;
        $time = $this->event->attendance->clock_in_time?->format('H:i') ?? now()->format('H:i');

        return (new MailMessage)
            ->subject("⚠ Geofence failure: {$user->name}")
            ->line("**{$user->name}** clocked in at {$time} but was OUTSIDE the required geofence radius.")
            ->line('GPS: ' . ($this->event->lat ?? 'n/a') . ', ' . ($this->event->lng ?? 'n/a'));
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'user_id'       => $this->event->user->id,
            'user_name'     => $this->event->user->name,
            'attendance_id' => $this->event->attendance->id,
            'lat'           => $this->event->lat,
            'lng'           => $this->event->lng,
            'clock_in_time' => $this->event->attendance->clock_in_time?->toIso8601String(),
        ];
    }
}
