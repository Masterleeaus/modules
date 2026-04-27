<?php

namespace Modules\Recruit\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifies an employee that one of their compliance fields (Police Check,
 * WWCC, Insurance) on their employee profile is about to expire.
 */
class RecruitComplianceExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $field;
    public string $label;
    public string $expiryDate;
    public int    $daysUntilExpiry;

    public function __construct(
        string $field,
        string $label,
        string $expiryDate,
        int    $daysUntilExpiry
    ) {
        $this->field           = $field;
        $this->label           = $label;
        $this->expiryDate      = $expiryDate;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("Action Required: {$this->label} expires in {$this->daysUntilExpiry} day(s)")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your **{$this->label}** is due to expire on **{$this->expiryDate}** ({$this->daysUntilExpiry} day(s) remaining).")
            ->line('Please renew this document and upload the updated copy to keep your employee profile compliant.')
            ->action('View My Profile', url('/account/employees/profile'))
            ->line('If you have already renewed this document, please update the expiry date in the system.');
    }

    public function toArray($notifiable): array
    {
        return [
            'field'             => $this->field,
            'label'             => $this->label,
            'expiry_date'       => $this->expiryDate,
            'days_until_expiry' => $this->daysUntilExpiry,
        ];
    }
}
