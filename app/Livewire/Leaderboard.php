<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Leaderboard extends Component
{
    public function tick(): void
    {
        $this->dispatch('leaderboard-refresh');
    }

    public function render(): View
    {
        return view('components.leaderboard');
    }
}
