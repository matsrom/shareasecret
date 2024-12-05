<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Crypt;

class ShowSecret extends Component
{

    public $secret;
    public $decryptedMessage;
    public $messageKey;

    protected $listeners = ['decryptText', 'decryptFile'];

    public function mount($secret)
    {
        $this->messageKey = request()->query('key');


        // if(auth()->user() && $secret->user_id === auth()->user()->id){
            
        //     //$this->dispatch('decryptSecretWhenLoggedIn', secret: $this->secret, messageKey: $this->messageKey, masterKey: auth()->user()->master_key);
        // }
        // else
        if($secret->secret_type === 'text'){
            $this->decryptText($this->messageKey);
        }
        else if($secret->secret_type === 'file'){
            $this->messageKey = request()->query('key');
        }
        else{
            abort(403, 'Unauthorized');
        }
            
    }


    function decryptText($messageKey){
        $iv = base64_decode($this->secret->message_iv);
        $tag = base64_decode($this->secret->message_tag);
        $encryptedMessage = base64_decode($this->secret->message);

        // Desencriptar el mensaje usando AES-256-GCM
        $decryptedMessage = openssl_decrypt(
            $encryptedMessage,
            'aes-256-gcm',
            $messageKey,
            0,
            $iv,
            $tag
        );



        if ($decryptedMessage === false) {
            abort(500, 'Failed to decrypt the message.');
        }
        $this->decryptedMessage = $decryptedMessage;

    }

    public function decryptFile($messageKey)
    {
        $filePath = storage_path('app/public/' . $this->secret->message);
        
        if (!file_exists($filePath)) {
            abort(404, 'Archivo no encontrado.');
        }

        $encryptedFileContents = file_get_contents($filePath);
        


        $iv = base64_decode($this->secret->message_iv);
        $tag = base64_decode($this->secret->message_tag);

        $decryptedFileContents = openssl_decrypt(
            $encryptedFileContents,
            'aes-256-gcm',
            $messageKey,
            0,
            $iv,
            $tag
        );

        if ($decryptedFileContents === false) {
            abort(500, 'Error al desencriptar el archivo.');
        }

        $originalFilename = Crypt::decryptString($this->secret->original_filename);

        return response()->streamDownload(function () use ($decryptedFileContents) {
            echo $decryptedFileContents;
        }, $originalFilename, [
            'Content-Type' => mime_content_type(storage_path('app/public/' . $this->secret->message))
        ]);
    }

    public function render()
    {
        return view('livewire.show-secret');
    }
}