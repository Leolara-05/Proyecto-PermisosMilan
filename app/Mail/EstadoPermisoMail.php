<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EstadoPermisoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $permiso;
    public $estado;

    /**
     * Create a new message instance.
     */
    public function __construct($permiso, $estado)
    {
        $this->permiso = $permiso;
        $this->estado = $estado;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Estado de tu Permiso')
                    ->view('emails.estado_permiso');
    }
}
