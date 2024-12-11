<?php

namespace App\Livewire;

use App\Models\Secret;
use Livewire\Component;

class DeleteSecret extends Component
{
    public $secretId;
    public $secret;

    // public function mount($secretId)
    // {
    //     $this->secretId = $secretId;
    //     $this->secret = Secret::find($secretId);
    // }

    public function deleteSecret()
    {
        $this->secret->delete();
        $this->dispatch('secretDeleted');
        $this->dispatch('show-toast', ['message' => 'Secret deleted successfully', 'class' => 'toast-success']);
        
    }

    public function render()
    {
        return view('livewire.delete-secret');
    }
}
