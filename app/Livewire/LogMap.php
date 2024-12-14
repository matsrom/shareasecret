<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SecretLog;

class LogMap extends Component
{
    public $secretId;


    public function mount($secretId)
    {
        $this->secretId = $secretId;
    }

    public function render()
    {
        $locations = SecretLog::select('latitude', 'longitude', 'city', 'country')
            ->where('secret_id', $this->secretId)
            ->get();

        return view('livewire.log-map', ['locations' => $locations]);
    }
}
