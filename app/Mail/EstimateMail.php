<?php

namespace App\Mail;

use App\Models\Estimate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EstimateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Estimate $estimate) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Estimate: {$this->estimate->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.estimate',
            with: [
                'url' => route('estimates.public', $this->estimate->token),
            ],
        );
    }
}
