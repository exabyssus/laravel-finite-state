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

    /**
     * @param string $stateTo
     * @return \Illuminate\Support\Collection
     */
    public function findHistoricalStatesByTo(string $stateTo)
    {
        return $this->transitionHistory->where('to', $stateTo);
    }

    /**
     * @param string $transition
     * @return \Illuminate\Support\Collection
     */
    public function findHistoricalStatesByTransition(string $transition)
    {
        return $this->transitionHistory->where('transition', $transition);
    }

    /**
     * @param string $stateTo
     * @return bool
     */
    public function hasStateInHistory(string $stateTo): bool
    {
        return $this->findHistoricalStatesByTo($stateTo)->isNotEmpty();
    }

    /**
     * @param string $transition
     * @return bool
     */
    public function hasTransitionInHistory(string $transition): bool
    {
        return $this->findHistoricalStatesByTransition($transition)->isNotEmpty();
    }
}
