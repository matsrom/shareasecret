<?php

namespace App\Livewire;

use App\Models\Secret;
use Livewire\Component;
use App\Enums\SecretType;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;



class SecretForm extends Component
{
    use WithFileUploads;
    
    // Secret Type
    public SecretType $secret_type;

    // Secret
    public $text_type;
    public $length;
    public $useCapitals;
    public $useNumbers;
    public $useSymbols;
    public $secret;

    // Settings
    public $daysLeft;
    public $clicksLeft;
    public $allowManualDelete;
    public $password;
    public $keepTrack;
    public $alias;




    public function mount()
    {
        //
        $this->secret_type = SecretType::Text;
        $this->text_type = 'manual';
        $this->useCapitals = false;
        $this->useNumbers = false;
        $this->useSymbols = false;

        $this->allowManualDelete = false;
        $this->keepTrack = false;
    }

    public function selectType(string $type)
    {
        $this->secret_type = SecretType::from($type);
    }

    public function toggle()
    {
        $this->text_type = $this->text_type == 'manual' ? 'automatic' : 'manual';
        $this->secret = '';
    }

    public function createAutomaticText()
    {
        if ($this->secret_type === 'text' && $this->text_type === 'automatic') {
            $this->validate([
                'length' => 'required|numeric|min:1',
            ]);
            $this->secret = rand(1000, 9999);  
            $this->dispatch('some-event');
        }

        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()';

        $secret = '';
        if ($this->useCapitals) {
            $characters .= $uppercase;
        }
        if ($this->useNumbers) {
            $characters .= $numbers;
        }
        if ($this->useSymbols) {
            $characters .= $symbols;
        }

        
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $this->length; $i++) {
            $secret .= $characters[random_int(0, $charactersLength - 1)];
        }

        $this->secret = str_shuffle($secret);;
    }



    public function updatedLength()
    {
        // Solo valida si se cumplen las condiciones específicas
        if ($this->secret_type === 'text' && $this->text_type === 'automatic') {
            $this->validate([
                'length' => 'required|numeric|min:1',
            ], [
                'length.required' => 'La longitud es obligatoria cuando el tipo de texto es automático.',
                'length.numeric' => 'La longitud debe ser un número.',
                'length.min' => 'La longitud debe ser al menos 1.',
            ]);          
        }
    }

    public function toggleKeepTrack()
    {
        $this->keepTrack = $this->keepTrack ? '' : '1';
        if(!$this->keepTrack) {
            $this->alias = '';
        }
    }

    public function createSecret()
{
    // Validación para tipo de secreto de texto
    if ($this->secret_type === SecretType::Text) {
        $this->validate([
            'secret' => 'required',
            'daysLeft' => 'nullable|numeric|min:1|required_without:clicksLeft',
            'clicksLeft' => 'nullable|numeric|min:1|required_without:daysLeft',
            'allowManualDelete' => 'nullable|boolean',
            'password' => 'nullable|string',
            'keepTrack' => 'nullable|boolean',
            'alias' => 'nullable|required_if:keepTrack,true|string',
        ], [
            'daysLeft.required_without' => 'You must specify days or clicks to expire.',
            'clicksLeft.required_without' => 'You must specify days or clicks to expire.',
            'alias.required_if' => 'Specify a name to identify the secret.',
        ]);
    }
    // Validación para tipo de secreto de archivo
    elseif ($this->secret_type === SecretType::File) {
        $this->validate([
            'secret' => 'required|file|max:2048', // Validación de tamaño máximo en KB
            'daysLeft' => 'nullable|numeric|min:1|required_without:clicksLeft',
            'clicksLeft' => 'nullable|numeric|min:1|required_without:daysLeft',
            'allowManualDelete' => 'nullable|boolean',
            'password' => 'nullable|string',
            'keepTrack' => 'nullable|boolean',
            'alias' => 'nullable|required_if:keepTrack,true|string',
        ], [
            'secret.required' => 'You must upload a file to share.',
            'daysLeft.required_without' => 'You must specify days or clicks to expire.',
            'clicksLeft.required_without' => 'You must specify days or clicks to expire.',
            'alias.required_if' => 'Specify a name to identify the secret.',
        ]);
    }

    // Datos base
    $data = [
        'secret_type' => $this->secret_type->value,
        'days_remaining' => $this->daysLeft,
        'clicks_remaining' => $this->clicksLeft,
        'allow_manual_deletion' => $this->allowManualDelete,
        'is_password_protected' => $this->password ? true : false,
        'password_hash' => $this->password ? bcrypt($this->password) : null,
    ];

    if ($this->daysLeft) $data['views_expiration'] = true;
    if ($this->clicksLeft) $data['clicks_expiration'] = true;

    if (auth()->user() && $this->keepTrack) {
        $data['keep_track'] = true;
        $data['user_id'] = auth()->user()->id;
        $data['alias'] = $this->alias;
    }

    // Generar las claves de mensaje: completa y corta
    $fullMessageKey = Str::random(32); // Clave completa
    $shortMessageKey = substr(hash('sha256', $fullMessageKey), 0, 8); // Clave corta de 8 caracteres

    // Procesar el secreto según su tipo (texto o archivo)
    if ($this->secret_type === SecretType::Text) {
        // Encriptar el mensaje de texto usando `fullMessageKey`
        $iv = random_bytes(12);
        $encryptedMessage = openssl_encrypt($this->secret, 'aes-256-gcm', $fullMessageKey, 0, $iv, $tag);

        $data['message'] = base64_encode($encryptedMessage);
        $data['message_iv'] = base64_encode($iv);
        $data['message_tag'] = base64_encode($tag);
    } elseif ($this->secret_type === SecretType::File) {
        $originalFilename = $this->secret->getClientOriginalName();

        // Encriptar el archivo
        $iv = random_bytes(12);
        $fileContents = file_get_contents($this->secret->getRealPath());
        $encryptedFileContents = openssl_encrypt($fileContents, 'aes-256-gcm', $fullMessageKey, 0, $iv, $tag);

        $path = 'secrets/' . Str::random(40) . '.enc';
        Storage::disk('public')->put($path, $encryptedFileContents);

        $data['message'] = $path;
        $data['message_iv'] = base64_encode($iv);
        $data['message_tag'] = base64_encode($tag);

        // Encripta el nombre original
        $data['original_filename'] = Crypt::encryptString($originalFilename);
    }

    $data['message_key'] = Crypt::encryptString($fullMessageKey);
    $data['short_message_key'] = $shortMessageKey;
    $data['url_identifier'] = Str::random(8);

    $secret = Secret::create($data);

    $this->reset(['secret', 'daysLeft', 'clicksLeft', 'password', 'keepTrack', 'alias']);
    
    return redirect()->route('secrets.success', ['secret' => $secret]);
}



    public function render()
    {
        return view('livewire.secret-form');
    }
}
