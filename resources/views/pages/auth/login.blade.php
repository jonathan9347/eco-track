<x-layouts::auth.split :title="__('Log in')">
    <x-slot:aside>
        <div class="flex h-full flex-col gap-4">
            <div class="overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/8 shadow-[0_20px_60px_rgba(0,0,0,0.2)] backdrop-blur-sm">
                <img
                    src="{{ asset('assets/classroom.jpg') }}"
                    alt="{{ __('Eco dashboard preview placeholder') }}"
                    class="h-48 w-full object-cover sm:h-56 lg:h-[18rem]"
                />
            </div>

            <div class="max-w-xl space-y-2.5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-100/80 sm:text-sm">
                    {{ __('Welcome back') }}
                </p>
                <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-[2rem] lg:text-[2.2rem]">
                    {{ __('Pick up where your sustainability progress left off.') }}
                </h1>
                <p class="max-w-lg text-sm leading-6 text-emerald-50/78">
                    {{ __('Sign in to review classroom activity, check your latest achievements, and keep building greener routines day by day.') }}
                </p>
                <div class="grid gap-2 pt-1 text-xs text-emerald-50/82 sm:grid-cols-2 sm:text-sm">
                    <div class="rounded-2xl border border-white/12 bg-white/8 px-3 py-2.5">
                        {{ __('See your most recent eco actions and progress updates fast.') }}
                    </div>
                    <div class="rounded-2xl border border-white/12 bg-white/8 px-3 py-2.5">
                        {{ __('Stay connected to classroom goals, rankings, and shared wins.') }}
                    </div>
                </div>
            </div>
        </div>
    </x-slot:aside>

    <div class="flex flex-col gap-4">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-3">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end pt-1">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth.split>
