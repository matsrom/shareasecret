<?php

namespace App\Http\Controllers;

use App\Models\Secret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SecretController extends Controller
{
    public function create()
    {
        return view('home');
    }

    public function success(Secret $secret){
        $shareLink = url("secret/{$secret->url_identifier}?key={$secret->short_message_key}");

        return view('secrets.success', ['shareLink' => $shareLink, 'secret' => $secret]);
    }

    public function show(Request $request, $url_identifier)
    {
        // Obtener la clave corta short_message_key de la URL
        $shortMessageKey = $request->query('key');

        // Buscar el secreto en la base de datos usando el url_identifier
        $secret = Secret::where('url_identifier', $url_identifier)->firstOrFail();

        // Validar el short_message_key
        if ($secret->short_message_key !== $shortMessageKey) {
            abort(403, 'Invalid access key.');
        }

        // Desencriptar message_key completa
        $fullMessageKey = Crypt::decryptString($secret->message_key);

        if ($secret->secret_type === 'text') {
            // Proceso para mensajes de texto
            $iv = base64_decode($secret->message_iv);
            $tag = base64_decode($secret->message_tag);
            $encryptedMessage = base64_decode($secret->message);

            // Desencriptar el mensaje usando AES-256-GCM
            $decryptedMessage = openssl_decrypt(
                $encryptedMessage,
                'aes-256-gcm',
                $fullMessageKey,
                0,
                $iv,
                $tag
            );

            if ($decryptedMessage === false) {
                abort(500, 'Failed to decrypt the message.');
            }

            // Retornar la vista con el mensaje desencriptado
            return view('secrets.show', ['message' => $decryptedMessage]);

        } elseif ($secret->secret_type === 'file') {
            
            $filePath = storage_path('app/public/' . $secret->message);

            if (!file_exists($filePath)) {
                abort(404, 'File not found.');
            }

            $encryptedFileContents = file_get_contents($filePath);

            $iv = base64_decode($secret->message_iv);
            $tag = base64_decode($secret->message_tag);

            $decryptedFileContents = openssl_decrypt(
                $encryptedFileContents,
                'aes-256-gcm',
                $fullMessageKey,
                0,
                $iv,
                $tag
            );

            if ($decryptedFileContents === false) {
                abort(500, 'Failed to decrypt the file.');
            }

            // Desencripta el nombre original del archivo
            $originalFilename = Crypt::decryptString($secret->original_filename);

            return response($decryptedFileContents)
                ->header('Content-Type', mime_content_type($filePath))
                ->header('Content-Disposition', 'attachment; filename="' . $originalFilename . '"');
        } else {
            abort(500, 'Unknown secret type.');
        }
    }

}
