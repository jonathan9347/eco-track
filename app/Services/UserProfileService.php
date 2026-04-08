<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Contract\Firestore;
use Throwable;

class UserProfileService
{
    public function __construct(
        protected Firestore $firestore,
    ) {
    }

    public function syncUser(User $user, array $attributes = []): void
    {
        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'classroom' => $user->classroom,
            'is_admin' => (bool) $user->is_admin,
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => now()->toISOString(),
            ...$attributes,
        ];

        $this->database()
            ->collection('users')
            ->document((string) $user->id)
            ->set($payload, ['merge' => true]);

        $this->forgetCachedProfile($user);
    }

    public function profileImage(User $user): ?string
    {
        $profile = $this->profile($user);

        $image = $profile['profile_image'] ?? null;

        return is_string($image) && $image !== '' ? $image : null;
    }

    public function profile(User $user): array
    {
        return Cache::remember(
            $this->cacheKey($user),
            now()->addMinutes(10),
            function () use ($user): array {
                try {
                    $snapshot = $this->database()
                        ->collection('users')
                        ->document((string) $user->id)
                        ->snapshot();

                    return $snapshot->exists() ? $snapshot->data() : [];
                } catch (Throwable) {
                    return [];
                }
            },
        );
    }

    public function forgetCachedProfile(User $user): void
    {
        Cache::forget($this->cacheKey($user));
    }

    protected function cacheKey(User $user): string
    {
        return 'user_profile_firestore_'.$user->id;
    }

    protected function database()
    {
        return $this->firestore->database();
    }
}
