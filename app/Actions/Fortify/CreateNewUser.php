<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Services\UserProfileService;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(
        protected FirebaseAuth $auth,
        protected UserProfileService $userProfileService,
    ) {
    }

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'classroom' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'classroom' => $input['classroom'],
            'password' => $input['password'],
        ]);

        $this->auth->createUser([
            'email' => $input['email'],
            'password' => $input['password'],
            'displayName' => $input['name'],
        ]);

        $this->userProfileService->syncUser($user, [
            'profile_image' => null,
        ]);

        return $user;
    }
}
