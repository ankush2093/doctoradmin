<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquirySubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $isAdmin;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @param bool $isAdmin
     */
    public function __construct($data, $isAdmin = false)
    {
        $this->data = $data;
        $this->isAdmin = $isAdmin;
    }

 

    public function envelope(): Envelope
    {
        $subject = $this->isAdmin 
            ? 'New Gym Enquiry Received' 
            : 'Thank you for your enquiry - GymFit';

        return new Envelope(
            subject: $subject,
        );
    }

 
    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiry-submitted',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}