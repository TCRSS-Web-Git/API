<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestAttach extends Mailable
{
    use Queueable, SerializesModels;

    protected $resume;

    protected $certs;

    /**
     * Create a new message instance.
     */
    public function __construct($resume, $certs)
    {
        $this->resume = $resume;
        $this->certs = $certs;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Attach',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.test-attach',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [
            Attachment::fromStorageDisk(config('filesystems.temporary_disk'), $this->resume)
                ->as('resume.pdf')
                ->withMime('application/pdf'),
        ];
        array_push($attachments, Attachment::fromStorageDisk(config('filesystems.temporary_disk'), $this->certs[0])
            ->as('cert1.pdf')
            ->withMime('application/pdf'));
        array_push($attachments, Attachment::fromStorageDisk(config('filesystems.temporary_disk'), $this->certs[1])
            ->as('cert2.pdf')
            ->withMime('application/pdf'));

        return $attachments;
    }
}
