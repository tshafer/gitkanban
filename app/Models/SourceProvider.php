<?php

namespace App\Models;

use App\Traits\UsedByTeams;
use Facades\App\Source\SourceProviderClientFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SourceProvider
 *
 * @property int $id
 * @property int $team_id
 * @property string|null $name
 * @property string|null $type
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Site[] $sites
 * @property-read int|null $sites_count
 * @property-read \App\Models\Team|null $team
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereUpdatedAt($value)
 *
 * @property int $project_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereProjectId($value)
 *
 * @property string|null $token
 * @property string|null $unique_id
 * @property string|null $label
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceProvider whereUniqueId($value)
 */
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
     * {@inheritdoc}
     */
    protected $hidden = ['meta', 'created_at', 'updated_at', 'user_id', 'token', 'team_id'];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
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
