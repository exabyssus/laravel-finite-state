<?php

namespace Exabyssus\StateMachine\Traits;

use Exabyssus\StateMachine\StateMachine;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasStateMachine
{
    /**
     * @var StateMachine $stateMachine
     */
    private $stateMachine;


    /**
     * @return StateMachine
     */
    private function stateMachine(): StateMachine
    {
        if (! $this->stateMachine) {
            $this->stateMachine = new StateMachine($this);
        }

        return $this->stateMachine;
    }

    /**
     * @param string $transition
     * @return bool
     * @throws \StateMachine\Exceptions\StateMachineException
     */
    public function transitionAllowed(string $transition): bool
    {
        return $this->stateMachine()->can($transition);
    }

    /**
     * @param string $transition
     * @return void
     * @throws \StateMachine\Exceptions\StateMachineException
     */
    public function transition(string $transition): void
    {
        $this->stateMachine()->apply($transition);
    }

    /**
     * @return array
     */
    public function getPossibleTransitions(): array
    {
        return $this->stateMachine()->getPossibleTransitions();
    }
}