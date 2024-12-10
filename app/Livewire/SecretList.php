<?php

namespace App\Livewire;

use App\Models\Secret;
use Livewire\Component;

class SecretList extends Component
{

    public $secrets;
    protected $listeners = ['secretDeleted' => 'render'];


    public function render()
    {
        $this->secrets = Secret::where('user_id', auth()->user()->id)
                        ->where('keep_track', 1)
                        ->get();

        return view('livewire.secret-list', ['secrets' => $this->secrets]);
    }
}

