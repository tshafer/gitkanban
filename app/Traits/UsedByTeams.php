<?php

namespace App\Traits;

use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UsedByTeams
{
    /**
     * @return void
     */
    protected static function bootUsedByTeams()
    {
        static::saving(
            function (Model $model) {
                if (auth()->guest()) {
                    return;
                }
                if (! isset($model->team_id) && isset(user()->current_team)) {
                    $model->team_id = user()->current_team->id;
                }
            }
        );
    }

    /**
     * Assign a team to the model.
     *
     * @return $this
     */
    public function removeFromTeam(): static
    {
        $this->team()->dissociate();

        return $this;
    }

    /**
     * @return belongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Assign a team to the model.
     *
     * @param  Team  $team the team
     * @return self
     */
    public function addToTeam(Team $team)
    {
        $this->team()->associate($team);

        return $this;
    }

    /**
     * Do not change!!!!
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Team>
     */
    public function owner()
    {
        return $this->team->owner();
    }
}
