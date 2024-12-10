<?php

namespace App\Livewire;

use App\Models\Secret;
use Livewire\Component;
use App\Models\SecretLog;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class ShowSecret extends Component
{

    public $secret;
    public $decryptedMessage;
    public $messageKey;

    public $passwordProtected;
    public $manualDeletion;
    public $password;
    public $passwordError = false;


    protected $listeners = ['decryptText', 'decryptFile'];

    public function mount($secret)
    {


        $currentDate = now();
        $expirationDate = $secret->created_at->addDays($secret->days_remaining);

        if (($secret->clicks_expiration && $secret->clicks_remaining <= 0) || ($secret->days_expiration && $currentDate->greaterThan($expirationDate))) {
            $this->createSecretLog(false);
            return redirect(route('secrets.create'))->with('status', [
                'message' => 'The secret has expired',
                'class' => 'toast-danger',
            ]);
        }

        $this->messageKey = request()->query('key');

        if ($secret->secret_type === 'text') {
            $this->decryptText($this->messageKey);
        } else if ($secret->secret_type === 'file') {
            $this->messageKey = request()->query('key');
        } else {
            abort(403, 'Unauthorized');
        }

        $this->passwordProtected = $secret->is_password_protected;
        $this->manualDeletion = $secret->allow_manual_deletion;

        if (!$this->passwordProtected) {
            $this->updateSecretClicks($secret);
            $this->createSecretLog(true);
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

    public function showSecret()
    {
        if (Hash::check($this->password, $this->secret->password_hash)) {
            $this->passwordProtected = false;
            if($this->secret->clicks_expiration){
                $this->updateSecretClicks($this->secret);
            }
            
            $this->render();
            $this->createSecretLog(true);
        }
        else{
            $this->passwordError = "Incorrect password";
            $this->createSecretLog(false);
        }
    }

    private function updateSecretClicks(Secret $secret): void
    {
        if($secret->clicks_expiration){
            $secret->clicks_remaining--;
            $secret->save();
        }
       
    }

    public function deleteSecret()
    {
        $this->secret->clicks_remaining = 0;
        $this->secret->days_remaining = 0;
        $this->secret->save();

        $this->redirect(route('secrets.create'));
    }

    public function createSecretLog($isSuccessful){
        $secretLog = new SecretLog();
        $secretLog->secret_id = $this->secret->id;
        $secretLog->ip_address = request()->ip();
        $secretLog->browser = Agent::browser() . ' ' . Agent::version(Agent::browser());
        $secretLog->os = Agent::platform() . ' ' . Agent::version(Agent::platform());
        $secretLog->device = Agent::device();
        $secretLog->country = request()->header('CF-IPCountry') ?? 'Unknown';
        $secretLog->city = request()->header('CF-IPCity') ?? 'Unknown';
        $secretLog->access_date = now();
        $secretLog->is_successful = $isSuccessful;
        $secretLog->save();
    }

    public function render()
    {
        return view('livewire.show-secret');
    }
}