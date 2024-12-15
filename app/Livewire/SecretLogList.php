<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class SecretLogList extends Component
{
    use WithPagination;

    public $secret;
    public $sortField = 'access_date';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $secretLogs = $this->secret->secretLogs()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);

        return view('livewire.secret-log-list', ['secretLogs' => $secretLogs]);
    }
}
