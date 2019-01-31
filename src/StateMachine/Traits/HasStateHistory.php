<?php

namespace Exabyssus\StateMachine\Traits;

use Exabyssus\StateMachine\Models\StateTransition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasStateHistory
{
    /**
     * @return HasMany
     */
    public function transitionHistory(): HasMany
    {
        return $this->morphMany(StateTransition::class);
    }

    /**
     * @param string $transition
     * @param string $to
     * @return void
     */
    public function afterStateChange(string $transition, string $to)
    {
        $this->addHistoryLine([
            "transition" => $transition,
            "to" => $to
        ]);
    }

    /**
     * @param array $transitionData
     * @return void
     */
    protected function addHistoryLine(array $transitionData)
    {
        $transitionData['user_id'] = Auth::id();

        $this->transitionHistory()->create($transitionData);
    }
}