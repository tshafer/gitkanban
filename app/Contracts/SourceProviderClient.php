<?php

namespace App\Contracts;

use App\Models\Deployment;
use App\Models\Hook;

interface SourceProviderClient
{
    /**
     * Determine if the source control credentials are valid.
     */
    public function valid(): bool;

    /**
     * Validate the given repository and branch are valid.
     */
    public function validRepository(string $repository, string $branch): bool;

    /**
     * Validate the given repository and commit hash are valid.
     */
    public function validCommit(string $repository, string $hash): bool;

    /**
     * Get the latest commit hash for the given repository and branch.
     */
    public function latestHashFor(string $repository, string $branch): string;

    /**
     * Get the tarball URL for the given deployment.
     *
     * @param  Deployment  $deployment
     * @return string
     */
    // public function tarballUrl(Deployment $deployment);

    /**
     * Publish the given hook.
     *
     * @return void
     */
    public function publishHook(Hook $hook);

    /**
     * Determine if the given hook payload is a test.
     */
    public function isTestHookPayload(Hook $hook, array $payload): bool;

    /**
     * Determine if the given hook payload applies to the hook.
     */
    public function receivesHookPayload(Hook $hook, array $payload): bool;

    /**
     * Get the commit hash from the given hook payload.
     */
    public function extractCommitFromHookPayload(array $payload): string;

    /**
     * Unpublish the given hook.
     *
     * @return void
     **/
    public function unpublishHook(Hook $hook);
}
