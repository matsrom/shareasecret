<?php

namespace App\Livewire;

use Livewire\Component;

class SecretList extends Component
{
    public $secrets;

    protected $listeners = ['secretDeleted' => 'render'];

    public function mount($secrets)
    {
        $this->secrets = $secrets;
    }

    public function render()
    {
        return view('livewire.secret-list');
    }
}
