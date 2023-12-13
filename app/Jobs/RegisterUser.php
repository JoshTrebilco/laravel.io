<?php

namespace App\Jobs;

use App\Enums\NotificationType;
use App\Exceptions\CannotCreateUser;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\GetGithubAvatar;

final class RegisterUser
{
    public function __construct(
        private string $name,
        private string $email,
        private string $username,
        private string $githubId,
        private string $githubUsername
    ) {
    }

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            $request->name(),
            $request->emailAddress(),
            $request->username(),
            $request->githubId(),
            $request->githubUsername(),
        );
    }

    public function handle(): void
    {
        $this->assertEmailAddressIsUnique($this->email);
        $this->assertUsernameIsUnique($this->username);

        $user = new User([
            'name' => $this->name,
            'email' => $this->email,
            'username' => mb_strtolower($this->username),
            'github_id' => $this->githubId,
            'github_username' => $this->githubUsername,
            'twitter' => null,
            'type' => User::DEFAULT,
            'bio' => '',
            'remember_token' => '',
            'allowed_notifications' => [
                NotificationType::MENTION,
                NotificationType::REPLY,
            ],
            'custom_avatar_at' => $this->checkIfUserHasCustomAvatar(),
        ]);
        $user->save();
    }

    private function assertEmailAddressIsUnique(string $emailAddress)
    {
        try {
            User::findByEmailAddress($emailAddress);
        } catch (ModelNotFoundException $exception) {
            return true;
        }

        throw CannotCreateUser::duplicateEmailAddress($emailAddress);
    }

    private function assertUsernameIsUnique(string $username)
    {
        try {
            User::findByUsername($username);
        } catch (ModelNotFoundException $exception) {
            return true;
        }

        throw CannotCreateUser::duplicateUsername($username);
    }

    private function checkIfUserHasCustomAvatar(): ?string
    {
        $avatar = new GetGithubAvatar($this->githubId);
        $avatar->handle();

        return $avatar->hasCustomAvatar() ? now() : null;
    }
}
