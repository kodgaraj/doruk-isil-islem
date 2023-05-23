<?php

namespace App\Mail;

use Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Send extends Mailable
{
    use Queueable, SerializesModels;
    public $email,$mesaj,$baslik,$pathToFile;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email,$mesaj,$baslik,$pathToFile)
    {
        $this->email=$email;
        $this->mesaj=$mesaj;
        $this->baslik=$baslik;
        $this->pathToFile=$pathToFile;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->baslik,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [
            Attachment::fromPath($this->pathToFile)
            ->as($this->baslik .'.pdf')
            ->withMime('application/pdf')
        ];
    }
}
