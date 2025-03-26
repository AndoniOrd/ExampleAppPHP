<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use Illuminate\Http\Request;

class EmailTrackingController extends Controller
{
    public function track($id)
    {
        // Encuentra el registro del correo por su ID
        $mail = Mail::findOrFail($id);

        // Registra la apertura del correo (puedes ajustar según tus necesidades)
        $mail->increment('open_count');
        $mail->last_opened_at = now();
        $mail->save();

        // Devuelve una imagen de 1x1 píxel
        $image = imagecreatetruecolor(1, 1);
        imagesavealpha($image, true);
        $trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $trans_colour);
        ob_start();
        imagepng($image);
        $image_data = ob_get_clean();
        imagedestroy($image);

        return response($image_data, 200)
            ->header('Content-Type', 'image/png')
            ->header('Content-Length', strlen($image_data));
    }
}