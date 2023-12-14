<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GetGithubAvatar
{
    private $github_id;

    private $avatarUrl;

    public function __construct(string $github_id)
    {
        $this->github_id = $github_id;
    }

    public function handle()
    {
        $this->avatarUrl = "https://avatars.githubusercontent.com/u/{$this->github_id}?v=4";
    }

    public function url()
    {
        return $this->avatarUrl;
    }

    public function hasCustomAvatar()
    {
        $response = Http::head($this->avatarUrl);

        // if url is not valid return false
        if (! $response->successful()) {
            return false;
        }

        // if content type is not png return true
        if ($response->header('Content-Type') !== 'image/png') {
            return true;
        }

        // if the image is larger than 6KiB return true
        if ($response->header('Content-Length') > 6144) {
            return true;
        }

        return false;
    }
}
