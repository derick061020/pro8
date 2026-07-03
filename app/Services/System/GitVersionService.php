<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class GitVersionService
{
    public function resolve(): string
    {
        $fromGit = $this->fromGitDescribe();

        if ($fromGit !== '') {
            return $fromGit;
        }

        return $this->fromGitLabTagsApi() ?? '';
    }

    private function fromGitDescribe(): string
    {
        if (! is_dir(base_path('.git'))) {
            return '';
        }

        foreach (['safe.directory='.base_path(), 'safe.directory=*'] as $safeDirectory) {
            $version = $this->runGitDescribe($safeDirectory);

            if ($version !== '') {
                return $version;
            }
        }

        return '';
    }

    private function runGitDescribe(string $safeDirectory): string
    {
        $process = new Process(
            ['git', '-c', $safeDirectory, 'describe', '--tags'],
            base_path()
        );
        $process->setTimeout(15);
        $process->run();

        if (! $process->isSuccessful()) {
            return '';
        }

        return trim($process->getOutput());
    }

    private function fromGitLabTagsApi(): ?string
    {
        $token = config('git.token');

        if (! $token) {
            return null;
        }

        $response = Http::withHeaders([
            'PRIVATE-TOKEN' => $token,
        ])->get(config('git.project_tags_url'), [
            'per_page' => 1,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $tags = $response->json();

        if (! is_array($tags) || count($tags) === 0) {
            return null;
        }

        $tag = $tags[0]['name'] ?? null;

        if ($tag === null) {
            return null;
        }

        return $this->enrichTagWithCommit($tag) ?? $tag;
    }

    /**
     * Cuando git describe no corre (p. ej. PHP-FPM como www-data), arma una cadena
     * similar usando el último commit de la rama actual vía API.
     */
    private function enrichTagWithCommit(string $tag): ?string
    {
        $token = config('git.token');

        if (! $token) {
            return null;
        }

        $projectUrl = preg_replace('#/repository/tags.*$#', '', config('git.project_tags_url'));

        $response = Http::withHeaders([
            'PRIVATE-TOKEN' => $token,
        ])->get($projectUrl.'/repository/commits', [
            'per_page' => 1,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $commits = $response->json();

        if (! is_array($commits) || count($commits) === 0) {
            return null;
        }

        $shortId = $commits[0]['short_id'] ?? null;

        if (! $shortId) {
            return null;
        }

        return $tag.'-g'.$shortId;
    }
}
