<?php

namespace App\Models;

use App\Traits\UsedByTeams;
use Facades\App\Source\SourceProviderClientFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SourceProvider extends Model
{
    use UsedByTeams;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'label',
        'name',
        'type',
        'meta',
        'token',
        'unique_id',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'meta' => 'json',
        // 'token' => 'encrypted',
        // 'unique_id' => 'encrypted',
    ];

    /**
     * Total repositories.
     *
     * @return Attribute
     */
    public function totalRepositories(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->meta['total_private_repos'] + $this->meta['public_repos'] ?? 0,
        );
    }
    /**
     * {@inheritdoc}
     */
    // protected $hidden = ['meta', 'created_at', 'updated_at', 'user_id', 'token', 'team_id'];

    /**
     * Get a source control provider client for the provider.
     *
     * @return \App\Contracts\SourceProviderClient
     */
    public function client()
    {
        return SourceProviderClientFactory::make($this);
    }
}
