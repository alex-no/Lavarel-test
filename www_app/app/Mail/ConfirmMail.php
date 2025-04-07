<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class ConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The name of the recipient.
     * @var string
     */
    public $name;
    /**
     * The URL for email verification.
     * @var string
     */
    public $verifyUrl;

    /**
     * The view template for the email.
     */
    public $mailTemplate;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $verifyUrl)
    {
        $this->name = $name;
        $this->verifyUrl = $verifyUrl;
        $language = App::getLocale();
        $this->mailTemplate = config("mail.confirm_templates.$language") ?? config("mail.confirm_templates.uk");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->mailTemplate,
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
                    ->view($this->mailTemplate);
    }

    /**
     * Get the message's content definition.
     */
    public function render()
    {
        return $this->view($this->mailTemplate)
                    ->with([
                        'name' => $this->name,
                    ]);
    }
}