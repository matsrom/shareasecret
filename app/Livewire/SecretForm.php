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
    public $secret_id;
    public $text_type;
    public $length;
    public $useCapitals;
    public $useNumbers;
    public $useSymbols;
    public $secret;

    public $urlKey;
    // Settings
    public $daysLeft;
    public $clicksLeft;
    public $allowManualDelete;
    public $password;
    public $keepTrack;
    public $alias;

    // Share    
    public $shareLink;

    protected $listeners = ['storeSecret' => 'storeSecret'];


    public function mount()
    {
        $this->secret_id = Str::uuid()->toString();
        $this->secret = null;
        $this->daysLeft = null;
        $this->clicksLeft = null;
        $this->allowManualDelete = false;
        $this->password = null;
        $this->keepTrack = false;
        $this->alias = null;
        
        
        $this->secret_type = SecretType::Text;
        $this->text_type = 'manual';
        $this->useCapitals = false;
        $this->useNumbers = false;
        $this->useSymbols = false;
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
        $this->keepTrack = $this->keepTrack ? '0' : '1';
        if(!$this->keepTrack) {
            $this->alias = '';
        }
    }

    public function createSecret()
    {

        $this->clicksLeft = $this->clicksLeft === "" ? null : $this->clicksLeft;
        $this->daysLeft = $this->daysLeft === "" ? null : $this->daysLeft;


        // Validación para tipo de secreto de texto
        if ($this->secret_type === SecretType::Text) {
            $this->validate([
                'secret' => 'required',
                'daysLeft' => 'nullable|numeric|min:1|max:365|required_without:clicksLeft',
                'clicksLeft' => 'nullable|numeric|min:1|required_without:daysLeft',
                'allowManualDelete' => 'nullable|boolean',
                'password' => 'nullable|string',
                'keepTrack' => 'nullable|boolean',
                'alias' => 'nullable|required_if:keepTrack,true|string',
            ], [
                'daysLeft.required_without' => 'You must specify days or clicks to expire.',
                'clicksLeft.required_without' => 'You must specify days or clicks to expire.',
                'alias.required_if' => 'Specify an alias to identify the secret.',
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
            'id' => $this->secret_id,
            'secret_type' => $this->secret_type->value,
            'days_remaining' => $this->daysLeft ?: 0,
            'clicks_remaining' => $this->clicksLeft ?: 0,
            'allow_manual_deletion' => $this->allowManualDelete,
            'is_password_protected' => $this->password ? true : false,
            'password_hash' => $this->password ? bcrypt($this->password) : null,
            'url_identifier' => Str::random(16),
            'days_expiration' => !is_null($this->daysLeft) && $this->daysLeft > 0,
            'clicks_expiration' => !is_null($this->clicksLeft) && $this->clicksLeft > 0,
        ];

        if ($this->secret_type === SecretType::Text && $this->text_type === 'manual') {
            $data['text_type'] = 'manual';
        }elseif ($this->secret_type === SecretType::Text && $this->text_type === 'automatic') {
            $data['text_type'] = 'automatic';
        }elseif ($this->secret_type === SecretType::File) {
            $data['original_filename'] = $this->secret->getClientOriginalName();
        }

        if (auth()->user()) {
            $data['keep_track'] = $this->keepTrack;
            $data['user_id'] = auth()->user()->id;
            $data['alias'] = $this->alias;
        }      

        if($this->keepTrack) {
            $this->dispatch('encryptSecret', data: $data, masterKey: auth()->user()->master_key);
        }else{
            $this->dispatch('encryptSecret', data: $data);
        }
    }

    function storeSecret($data) {
        if(!auth()->user() || $data['keep_track'] == 0) {
            $data['message_key'] = "";
        }
        $secret = Secret::create($data);

        return redirect()->route('secrets.success', ['secret' => $secret]);
    }



    public function render()
    {
        return view('livewire.secret-form');
    }
}
