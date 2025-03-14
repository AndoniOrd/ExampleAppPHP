<?php

use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;

Route::get('/enviar-correo-de-prueba', function () {
    $datos = [
        'nombre' => 'Juan Pérez',
        'mensaje' => 'Este es un mensaje de prueba.'
    ];

    Mail::to('[email protected]')->send(new TestEmail($datos));

    return '¡Correo de prueba enviado!';
});
