<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PermisoMilan;
class PermisoAutorizadoMail extends Mailable
{
    use Queueable, SerializesModels;
    public $permiso;
    /**
     * Create a new message instance.
     */
    public function __construct(PermisoMilan $permiso)
    {
        $this->permiso = $permiso;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permiso Autorizado Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.permiso_autorizado',
        );
    }
    // public function build()
    // {
    //     return $this->subject('Permiso Autorizado')
    //                 ->view('emails.permiso_autorizado');
    // }
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
