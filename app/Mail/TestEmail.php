<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Datos que se pasarán a la vista del correo.
     *
     * @var array
     */
    public $datos;

    /**
     * Crear una nueva instancia del mensaje.
     *
     * @param array $datos
     * @return void
     */
    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.test')
            ->with('datos', $this->datos)
            ->subject('Asunto del correo de prueba');
    }
}