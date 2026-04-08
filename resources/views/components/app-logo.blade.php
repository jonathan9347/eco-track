@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Eco Track" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md ring-1 ring-zinc-200 dark:ring-zinc-700">
            <img
                src="{{ asset('assets/eco-track-auth-logo.png') }}"
                alt="{{ __('Eco Track logo') }}"
                class="size-full object-contain"
            />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Eco Track" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md ring-1 ring-zinc-200 dark:ring-zinc-700">
            <img
                src="{{ asset('assets/eco-track-auth-logo.png') }}"
                alt="{{ __('Eco Track logo') }}"
                class="size-full object-contain"
            />
        </x-slot>
    </flux:brand>
@endif
