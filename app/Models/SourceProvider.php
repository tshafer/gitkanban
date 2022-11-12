<?php

namespace App\Models;

use App\Traits\UsedByTeams;
use Facades\App\Source\SourceProviderClientFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
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
        'expires_in',
        'refresh_token',
        'meta_updated_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'meta' => AsArrayObject::class,
        'meta_updated_at' => 'datetime',
        // 'token' => 'encrypted',
        // 'unique_id' => 'encrypted',
    ];

    public function lastRefreshedHuman(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->meta_updated_at?->diffForHumans() ?? 'N/A',
        );
    }

    public function lastRefreshed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->meta_updated_at?->format('m/d/y h:i') ?? 'N/A',
        );
    }

    public function refresh(): void
    {
        $refreshData = $this->client()->refresh();

        $this->meta_updated_at = now();
        $this->meta['number_repos'] = $refreshData['number_repos'];
        $this->save();
    }

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
