<x-layouts::auth.split :title="__('Register')">
    <x-slot:aside>
        <div class="flex h-full flex-col gap-4">
            <div class="overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/8 shadow-[0_20px_60px_rgba(0,0,0,0.2)] backdrop-blur-sm">
                <img
                    src="{{ asset('assets/classroom.jpg') }}"
                    alt="{{ __('Students collaborating in a classroom') }}"
                    class="h-48 w-full object-cover sm:h-56 lg:h-[18rem]"
                />
            </div>

            <div class="max-w-xl space-y-2.5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-100/80 sm:text-sm">
                    {{ __('Start your eco journey') }}
                </p>
                <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-[2rem] lg:text-[2.2rem]">
                    {{ __('Create your account and begin tracking impact together.') }}
                </h1>
                <p class="max-w-lg text-sm leading-6 text-emerald-50/78">
                    {{ __('Join your classroom, monitor sustainable habits, and turn everyday actions into visible progress for your community.') }}
                </p>
                <div class="grid gap-2 pt-1 text-xs text-emerald-50/82 sm:grid-cols-2 sm:text-sm">
                    <div class="rounded-2xl border border-white/12 bg-white/8 px-3 py-2.5">
                        {{ __('Track carbon-saving habits and milestones in one place.') }}
                    </div>
                    <div class="rounded-2xl border border-white/12 bg-white/8 px-3 py-2.5">
                        {{ __('Collaborate with your class through shared sustainability goals.') }}
                    </div>
                </div>
            </div>
        </div>
    </x-slot:aside>

    <div class="flex flex-col gap-4">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-3">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Classroom -->
            <flux:select
                name="classroom"
                :label="__('Classroom')"
                :value="old('classroom')"
                placeholder="Select a classroom"
            >
                <option value="Section A">Section A</option>
                <option value="Section B">Section B</option>
                <option value="Section C">Section C</option>
                <option value="Section D">Section D</option>
            </flux:select>

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end pt-1">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
                </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth.split>
