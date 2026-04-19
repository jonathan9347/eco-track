<x-layouts::auth.split :title="__('Register')">
    <x-slot:aside>
        <div class="space-y-6">
            <div class="inline-flex items-center gap-3 rounded-full border border-white/20 bg-white/8 px-4 py-2 text-sm font-medium text-white/88 backdrop-blur-sm">
                <img
                    src="{{ asset('assets/eco-track-auth-logo.png') }}"
                    alt="{{ __('Eco Track icon') }}"
                    class="h-5 w-5 object-contain brightness-0 invert"
                />
                <span>{{ __('Eco-conscious tracking for students') }}</span>
            </div>

            <h1 class="max-w-md font-['Outfit'] text-5xl font-semibold leading-[0.94] tracking-[-0.05em] text-white sm:text-6xl lg:text-[4.9rem]">
                {{ __('Start Your Journey to Net Zero') }}
            </h1>
            <p class="max-w-lg text-base leading-7 text-white/82 sm:text-lg">
                {{ __('Track your carbon footprint, set personal goals, and make a real impact on our planet. Join Eco Track today.') }}
            </p>
            <div class="grid max-w-xl gap-3 pt-2 sm:grid-cols-2">
                <div class="rounded-[1.5rem] border border-white/12 bg-white/8 px-4 py-4 text-sm leading-6 text-white/78 backdrop-blur-sm">
                    {{ __('Set up your account and begin building greener habits with visible progress.') }}
                </div>
                <div class="rounded-[1.5rem] border border-white/12 bg-white/8 px-4 py-4 text-sm leading-6 text-white/78 backdrop-blur-sm">
                    {{ __('Keep the same registration fields while giving the page a cleaner modern feel.') }}
                </div>
            </div>
        </div>
    </x-slot:aside>

    <div class="auth-form-shell flex flex-col gap-4">
        <a href="{{ route('home') }}" class="mx-auto mb-1 flex items-center justify-center gap-2 text-[1.7rem] font-semibold tracking-[-0.03em] text-[#2c4b35]" wire:navigate>
            <img
                src="{{ asset('assets/eco-track-auth-logo.png') }}"
                alt="{{ __('Eco Track') }}"
                class="h-8 w-8 object-contain"
            />
            <span><span class="text-[#6f955f]">{{ __('Eco') }}</span> {{ __('Track') }}</span>
        </a>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-3">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Full Name')"
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
                :label="__('Email Address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="Email Address"
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

            <div class="grid gap-3 sm:grid-cols-2">
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
                    :label="__('Confirm Password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Confirm password')"
                    viewable
                />
            </div>

            <div class="flex items-center justify-end pt-1">
                <flux:button type="submit" variant="primary" class="auth-submit-btn w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-[#697260]">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link class="font-semibold text-[#4b6f4f]" :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth.split>
