<?php

namespace App\Services;

use App\Contracts\SourceProviderClient;
use App\Models\Hook;
use App\Models\SourceProvider;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class GitHub implements SourceProviderClient
{
    /**
     * Create a new GitHub service instance.
     */
    public function __construct(protected SourceProvider $source)
    {
    }

    /**
     * @return string
     */
    public function name()
    {
        return 'Github';
    }

    /**
     * Determine if the source control credentials are valid.
     */
    public function valid(): bool
    {
        try {
            $this->request('get', '/user/repos');

            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Make an HTTP request to GitHub.
     *
     * @param  string  $method
     * @param  string  $path
     * @return array
     */
    protected function request($method, $path, array $parameters = [])
    {
        $path = ltrim($path, '/');

        $path = 'https://api.github.com/'.$path;

        $response = HTTP::withHeaders(
            [
                'Accept' => 'application/vnd.github.v3+json',
            ]
        )
            ->withToken($this->token())->{$method}(
                $path,
                [
                    'json' => $parameters,
                ]
            );

        return $response->json();
    }

    /**
     * Get the authentication token for the provider.
     *
     * @return string
     */
    protected function token()
    {
        return Arr::get($this->source->meta, 'token');
    }

    /**
     * @param  string  $repository
     * @return string
     */
    public function pullPath($repository)
    {
        return 'https://github.com/'.$repository.'.git';
    }

    /**
     * Validate the given repository and branch are valid.
     */
    public function validRepository(string $repository, string $branch): bool
    {
        if (empty($repository)) {
            return false;
        }

        try {
            $response = $this->request('get', "/repos/{$repository}/branches");
        } catch (Exception) {
            return false;
        }

        if (empty($branch)) {
            return true;
        }

        return collect($response)->contains(fn ($b) => $b['name'] === $branch);
    }

    /**
     * Validate the given repository and commit hash are valid.
     */
    public function validCommit(string $repository, string $hash): bool
    {
        if (empty($repository) || empty($hash)) {
            return false;
        }

        try {
            $response = $this->request('get', "/repos/{$repository}/commits/{$hash}");
        } catch (Exception) {
            return false;
        }

        return $response['sha'] === $hash;
    }

    /**
     * Get the latest commit hash for the given repository and branch.
     */
    public function latestHashFor(string $repository, string $branch): string
    {
        return $this->request(
            'get',
            "/repos/{$repository}/commits?sha={$branch}&per_page=1"
        )[0]['sha'];
    }

    /**
     * @param  string  $repository
     * @param  string  $branch
     * @return string
     */
    public function commitInfo($repository, $branch)
    {
        return $this->request('get', "/repos/{$repository}/commits?sha={$branch}&per_page=1")[0];
    }

    /**
     * @param  string  $repository
     * @param  string  $branch
     * @return string
     */
    public function commitAuthor($repository, $branch)
    {
        return $this->request(
            'get',
            "/repos/{$repository}/commits?sha={$branch}&per_page=1"
        )[0]['commit']['author'];
    }

    /**
     * @param  string  $repository
     * @return string
     */
    public function sshUrl($repository)
    {
        return 'git@github.com:'.$repository.'.git';
    }

    /**
     * @param  string  $key
     * @return void
     */
    public function addServerKey($key)
    {
        $hasKey = collect($this->request('get', '/user/keys'))
            ->pluck('title')
            ->contains('CodeDeploy');
        if (! $hasKey) {
            $this->request(
                'post',
                '/user/keys',
                [
                    'title' => 'CodeDeploy',
                    'key' => $key,
                ]
            );
        }
    }

    /**
     * Publish the given hook.
     *
     * @return void
     */
    public function publishHook(Hook $hook)
    {
        $this->deleteHooksWithMatchingUrl($hook);

        $response = $this->request(
            'post',
            '/repos/'.$hook->project->repository.'/hooks',
            [
                'name' => 'web',
                'config' => [
                    'url' => $hook->url(),
                    'content_type' => 'json',
                ],
                'events' => ['push'],
                'active' => true,
            ]
        );

        $hook->update(
            [
                'published' => true,
                'meta' => array_merge(
                    $hook->meta,
                    [
                        'provider_hook_id' => $response['id'],
                    ]
                ),
            ]
        );
    }

    /**
     * Delete any hooks matching the given hooks URL.
     *
     * @return void
     */
    protected function deleteHooksWithMatchingUrl(Hook $hook)
    {
        if ($existingHook = $this->findHookWithMatchingUrl($hook)) {
            $this->deleteHookById($hook->project->repository, $existingHook['id']);
        }
    }

    /**
     * Find a hook by the given hook's URL.
     *
     * @return array|null
     */
    protected function findHookWithMatchingUrl(Hook $hook)
    {
        $url = $hook->url();

        return collect(
            $this->request('get', '/repos/'.$hook->project->repository.'/hooks')
        )->first(fn ($hook) => ($hook['config']['url'] ?? null) === $url);
    }

    /**
     * Delete a hook by the given repository and ID.
     *
     * @param  string  $repository
     * @param  string  $id
     * @return void
     */
    protected function deleteHookById($repository, $id)
    {
        $this->request('delete', '/repos/'.$repository.'/hooks/'.$id);
    }

    /**
     * Determine if the given hook payload applies to the hook.
     */
    public function receivesHookPayload(Hook $hook, array $payload): bool
    {
        return ! $this->isTestHookPayload($hook, $payload) &&
            $payload['ref'] === "refs/heads/{$hook->branch}" &&
            $payload['repository']['full_name'] === $hook->project->repository;
    }

    /**
     * Determine if the given hook payload is a test.
     */
    public function isTestHookPayload(Hook $hook, array $payload): bool
    {
        return isset($payload['zen']);
    }

    /**
     * Get the commit hash from the given hook payload.
     *
     * @return string|null
     */
    public function extractCommitFromHookPayload(array $payload): string
    {
        return $payload['head_commit']['id'] ?? null;
    }

    /**
     * Unpublish the given hook.
     *
     * @return void
     */
    public function unpublishHook(Hook $hook)
    {
        if (! ($providerHookId = $hook->meta['provider_hook_id'] ?? null)) {
            return;
        }

        $this->deleteHookById($hook->project->repository, $providerHookId);

        $hook->update(
            [
                'published' => false,
                'meta' => array_filter(
                    array_merge(
                        $hook->meta,
                        [
                            'provider_hook_id' => null,
                        ]
                    )
                ),
            ]
        );
    }
}
