<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Source\Hook
 *
 * @property int $id
 * @property Project $project
 * @property int $project_id
 * @property string $name
 * @property string $token
 * @property string $branch
 * @property bool $published
 * @property array $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Hook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hook query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hook whereBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hook whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hook whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hook whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hook wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hook whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hook whereUpdatedAt($value)
 */
class Hook extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'meta' => 'json',
        'published' => 'boolean',
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * Get the repository for the hook.
     *
     * @return string
     */
    public function repository()
    {
        return $this->project->repository;
    }

    /**
     * Get the project for the hook.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Publish the hook to the source control provider.
     *
     * @return void
     */
    public function publish(): void
    {
        $this->sourceProvider()->publishHook($this);
    }

    /**
     * Get the source control provider for the hook.
     *
     * @return BelongsTo
     */
    public function sourceProvider(): BelongsTo
    {
        return $this->project->source();
    }

    /**
     * Determine if the given hook payload is a test.
     *
     * @param  array  $payload
     * @return bool
     */
    public function isTest(array $payload): bool
    {
        return $this->sourceProvider()->isTestHookPayload($this, $payload);
    }

    /**
     * Determine if this hook responds to the given source provider event payload.
     *
     * @param  array  $payload
     * @return bool
     */
    public function receives(array $payload): bool
    {
        return ! $this->published || $this->sourceProvider()->receivesHookPayload($this, $payload);
    }

    /**
     * Delete the model from the database.
     *
     *
     * @throws Exception
     */
    public function delete(): ?bool
    {
        $this->unpublish();

        return parent::delete();
    }

    /**
     * Remove the hook from the source control provider.
     *
     * @return void
     */
    public function unpublish(): void
    {
        if ($this->published) {
            $this->sourceProvider()->unpublishHook($this);
        }
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'url' => $this->url(),
        ]);
    }

    /**
     * Get the URL to be used for hook deployments.
     *
     * @return string
     */
    public function url(): string
    {
        return url(config('app.url')."/api/hook-deployment/{$this->id}/{$this->token}");
    }
}
