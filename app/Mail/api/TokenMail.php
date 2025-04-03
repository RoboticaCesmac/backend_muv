<?php

namespace App\Mail\api;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TokenMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * O token que será enviado.
     *
     * @var string
     */
    public $token;

    /**
     * O nome do usuário.
     *
     * @var string
     */
    public $name;

    /**
     * O tempo de expiração do token em minutos.
     *
     * @var int
     */
    public $expiresInMinutes;

    /**
     * Create a new message instance.
     *
     * @param  string  $token
     * @param  string  $name
     * @param  int  $expiresInMinutes
     * @return void
     */
    public function __construct(string $token, string $name, int $expiresInMinutes = 60)
    {
        $this->token = $token;
        $this->name = $name;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu Token de Acesso',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.token',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
} 