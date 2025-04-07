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
    protected $viewTemplate;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $verifyUrl)
    {
        $this->name = $name;
        $this->verifyUrl = $verifyUrl;
        // Set the view template based on the user's language
        $language = App::getLocale();
        $this->viewTemplate = config("mail.confirm_templates.$language") ?? config("mail.confirm_templates.uk");
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
            view: $this->viewTemplate,
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
                    ->view($this->viewTemplate);
    }

    /**
     * Get the message's content definition.
     */
    public function render()
    {
        return $this->view($this->viewTemplate)
                    ->with([
                        'name' => $this->name,
                    ]);
    }
}