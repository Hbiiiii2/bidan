<?php

namespace App\Mail;

use App\Models\Child;
use App\Models\Immunization;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImmunizationRecallMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Child $child,
        public string $vaccineName,
        public string $nextDose,
        public Carbon $dueDate,
        public int $intervalDays,
        public Immunization $latestImmunization,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat imunisasi ' . $this->vaccineName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.immunization-recall',
        );
    }
}
