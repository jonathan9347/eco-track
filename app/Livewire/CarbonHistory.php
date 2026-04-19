<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CarbonHistory extends Component
{
    public ?int $userId = null;

    public function mount(): void
    {
        $this->userId = auth()->id();
    }

    public function render(): View
    {
        return view('components.carbon-history')
            ->layout('layouts.app', ['title' => 'My Carbon Logs']);
    }
}
