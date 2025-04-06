<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The name of the recipient.
     * @var string
     */
    public $name;
    public $verifyUrl;


    /**
     * Create a new message instance.
     */
    public function __construct($name, $verifyUrl)
    {
        $this->name = $name;
        $this->verifyUrl = $verifyUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'My Test Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.confirm',
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
    /**
     * Get the message's content definition.
     */
    public function build()
    {
        return $this->subject('Confirm email')
                    ->view('emails.confirm');
    }

    /**
     * Get the message's content definition.
     */
    public function render()
    {
        return $this->view('emails.confirm')
                    ->with([
                        'name' => $this->name,
                    ]);
    }
}