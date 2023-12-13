<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\View\Component;
use App\Services\GetGithubAvatar;
use Illuminate\Contracts\View\View;

class Avatar extends Component
{
    public User $user;
    public bool $unlinked;
    public string $url;
    public string $fallback;

    public function __construct(User $user, bool $unlinked = false)
    {
        $this->user = $user;
        $this->unlinked = $unlinked;
        $this->fallback = asset('https://laravel.io/images/laravelio-icon-gray.svg');

        $this->url = $this->getAvatarUrl();
    }

    public function render(): View
    {
        return view('components.avatar');
    }

    private function getAvatarUrl(): string
    {
        if ($this->user->githubUsername()) {
            $githubAvatar = new GetGithubAvatar($this->user->github_id);
            $githubAvatar->handle();

            return $githubAvatar->url();
        }

        return $this->fallback;
    }
}