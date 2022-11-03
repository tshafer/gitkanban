<?php

namespace App\Source;

use App\Models\SourceProvider;
use App\Services\BitBucket;
use App\Services\GitHub;
use App\Services\GitLab;
use InvalidArgumentException;

class SourceProviderClientFactory
{
    /**
     * Create a source control provider client instance for the given provider.
     *
     * @param  \App\Models\SourceProvider  $source
     * @return \App\Contracts\SourceProviderClient
     */
    public function make(SourceProvider $source)
    {
        return match ($source->type) {
            'github' => new GitHub($source),
            'gitlab' => new GitLab($source),
            'bitbucket' => new BitBucket($source),
            default => throw new InvalidArgumentException(__('Invalid source control provider type.')),
        };
    }
}
