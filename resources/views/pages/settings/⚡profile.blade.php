<?php

use App\Concerns\ProfileValidationRules;
use App\Services\UserProfileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules {
        profileRules as baseProfileRules;
    }
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $classroom = '';
    public ?string $currentProfileImage = null;
    public $profileImageUpload = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->classroom = Auth::user()->classroom ?? '';
        $this->currentProfileImage = app(UserProfileService::class)->profileImage(Auth::user());
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $profileAttributes = [];

        if ($this->profileImageUpload) {
            $profileAttributes['profile_image'] = $this->imageAsDataUrl($this->profileImageUpload);
        }

        app(UserProfileService::class)->syncUser($user, $profileAttributes);

        if (array_key_exists('profile_image', $profileAttributes)) {
            $this->currentProfileImage = $profileAttributes['profile_image'];
            $this->profileImageUpload = null;
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function removeProfileImage(): void
    {
        $user = Auth::user();

        app(UserProfileService::class)->syncUser($user, ['profile_image' => null]);

        $this->currentProfileImage = null;
        $this->profileImageUpload = null;

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }

    protected function profileRules(?int $userId = null): array
    {
        return [
            ...$this->baseProfileRules($userId),
            'profileImageUpload' => ['nullable', 'image', 'max:350'],
        ];
    }

    protected function imageAsDataUrl(TemporaryUploadedFile|UploadedFile $file): string
    {
        $mimeType = $file->getMimeType() ?: 'image/png';
        $contents = file_get_contents($file->getRealPath());

        return 'data:'.$mimeType.';base64,'.base64_encode($contents ?: '');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name, email address, and profile image')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="space-y-4 rounded-2xl border border-zinc-200 bg-zinc-50 p-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl bg-emerald-100 text-lg font-semibold text-emerald-700">
                        @if ($profileImageUpload)
                            <img
                                src="{{ $profileImageUpload->temporaryUrl() }}"
                                alt="{{ __('Profile image preview') }}"
                                class="h-full w-full object-cover"
                            >
                        @elseif ($currentProfileImage)
                            <img
                                src="{{ $currentProfileImage }}"
                                alt="{{ __('Current profile image') }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <span>{{ auth()->user()->initials() }}</span>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <div>
                            <flux:heading size="sm">{{ __('Profile image') }}</flux:heading>
                            <flux:text class="mt-1 text-sm text-zinc-500">
                                {{ __('Upload a JPG, PNG, WEBP, or GIF under 350 KB. It will be stored in Firestore as Base64.') }}
                            </flux:text>
                        </div>

                        <input
                            type="file"
                            wire:model="profileImageUpload"
                            accept="image/png,image/jpeg,image/webp,image/gif"
                            class="block w-full text-sm text-zinc-600 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-emerald-700"
                        >

                        @error('profileImageUpload')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if ($currentProfileImage || $profileImageUpload)
                            <flux:button type="button" variant="ghost" wire:click="removeProfileImage">
                                {{ __('Remove image') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <flux:input :label="__('Classroom')" :value="$classroom ?: __('Not set')" type="text" disabled />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
