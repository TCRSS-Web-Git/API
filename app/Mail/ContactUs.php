<?php

namespace App\Mail;

use App\Enums\DepartmentType;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    protected string $name;

    protected string $surname;

    protected string $phone;

    protected string $email;

    protected DepartmentType $departmentType;

    protected string $detail;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->surname = $data['surname'];
        $this->phone = $data['phone'];
        $this->email = $data['email'];
        $this->departmentType = DepartmentType::from($data['department_type']);
        $this->detail = $data['detail'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Contact Us From $this->name $this->surname",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact-us',
            with: [
                'name' => $this->name,
                'surname' => $this->surname,
                'phone' => $this->phone,
                'email' => $this->email,
                'department_type_en' => $this->departmentType->labelEn(),
                'department_type_th' => $this->departmentType->labelTh(),
                'detail' => $this->detail,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
