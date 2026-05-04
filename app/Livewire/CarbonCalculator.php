<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CarbonCalculator extends Component
{
    public ?int $userId = null;

    public array $transportOptions = [
        ['value' => 'walking', 'label' => 'Walking'],
        ['value' => 'jeepney', 'label' => 'Jeepney'],
        ['value' => 'bus', 'label' => 'Bus'],
        ['value' => 'tricycle', 'label' => 'Tricycle'],
        ['value' => 'car', 'label' => 'Car'],
    ];

    public array $dietOptions = [
        ['value' => 'meat', 'label' => 'Meat-heavy'],
        ['value' => 'average', 'label' => 'Average'],
        ['value' => 'vegetarian', 'label' => 'Vegetarian'],
        ['value' => 'plant_based', 'label' => 'Plant-based'],
    ];

    public function mount(): void
    {
        $this->userId = auth()->id();
    }

    public function render(): View
    {
        return view('components.carbon-calculator');
    }
}
