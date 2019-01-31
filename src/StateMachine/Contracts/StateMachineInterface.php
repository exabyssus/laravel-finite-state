<?php

namespace Exabyssus\StateMachine\Contracts;

interface StateMachineInterface
{
    /**
     * Can the transition be applied on the underlying object
     *
     * @param string $transition
     *
     * @return bool
     * @throws \StateMachine\Exceptions\StateMachineException If transition doesn't exist
     */
    public function can($transition): bool;

    /**
     * Applies the transition on the underlying object
     *
     * @param string $transition Transition to apply
     * @throws \StateMachine\Exceptions\StateMachineException If transition can't be applied or doesn't exist
     */
    public function apply($transition): void;

    /**
     * Returns the current state
     *
     * @return string
     * @throws \StateMachine\Exceptions\StateMachineException If state does not exist
     */
    public function getState(): string;

    /**
     * Returns the possible transitions
     *
     * @return array
     */
    public function getPossibleTransitions(): array;
}
