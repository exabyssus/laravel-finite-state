<?php

namespace Exabyssus\StateMachine\Traits;

use Exabyssus\StateMachine\Models\StateTransition;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait HasStateHistory
{
    /**
     * @return MorphMany
     */
    public function transitionHistory(): MorphMany
    {
        return $this->morphMany(StateTransition::class, 'owner');
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