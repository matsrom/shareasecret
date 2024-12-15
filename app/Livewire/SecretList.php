<?php

namespace App\Livewire;

use App\Models\Secret;
use Livewire\Component;
use Livewire\WithPagination;

class SecretList extends Component
{
    use WithPagination;

    public $sortColumn = 'created_at';
    public $sortDirection = 'asc';

    protected $listeners = ['secretDeleted' => 'render'];

    // Sort by column
    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $secrets = Secret::where('user_id', auth()->user()->id)
                        ->where('keep_track', 1)
                        ->orderBy($this->sortColumn, $this->sortDirection)
                        ->paginate(10);

        return view('livewire.secret-list', ['secrets' => $secrets]);
    }
}

