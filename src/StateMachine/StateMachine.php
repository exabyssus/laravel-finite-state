<?php

namespace Exabyssus\StateMachine;

use Exabyssus\StateMachine\Events\StateChanged;
use Exabyssus\StateMachine\Exceptions\StateMachineException;
use Exabyssus\StateMachine\Contracts\StateMachineInterface;

class StateMachine implements StateMachineInterface
{
    /**
     * @var object
     */
    protected $object;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param object $object Underlying object for the state machine
     *
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->config = config('state-machine.' . strtolower(class_basename($this->object)));
    }

    /**
     * {@inheritDoc}
     */
    public function can($transition): bool
    {
        if (! isset($this->config['transitions'][$transition])) {
            throw new StateMachineException(sprintf(
                'Transition "%s" does not exist on object "%s"',
                $transition,
                get_class($this->object)
            ));
        }

        if (! in_array($this->getState(), $this->config['transitions'][$transition]['from'])) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($transition): void
    {
        if (! $this->can($transition)) {
            throw new StateMachineException(sprintf(
                'Transition "%s" cannot be applied on state "%s" of object "%s"',
                $transition,
                $this->getState(),
                get_class($this->object)
            ));
        }

        $stateFrom = $this->config['transitions'][$transition]['from'];
        $stateTo = $this->config['transitions'][$transition]['to'];

        if (method_exists($this->object, 'beforeStateChange')) {
            $resolve = $this->object->beforeStateChange($transition, $stateFrom, $stateTo);

            if (! $resolve) {
                return;
            }
        }

        $this->setState($stateTo);

        event(new StateChanged($this->object, $this->config['transitions'][$transition]));

        if (method_exists($this->object, 'afterStateChange')) {
            $this->object->afterStateChange($transition, $stateTo);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getState(): string
    {
        $property = $this->config['state_property_name'];
        $value = $this->object->$property;

        if (! is_string($value)) {
            throw new StateMachineException('State not set for ' . get_class($this->object));
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getPossibleTransitions(): array
    {
        return array_filter(
            array_keys($this->config['transitions']),
            array($this, 'can')
        );
    }

    /**
     * Set a new state to the underlying object
     *
     * @param string $state
     *
     * @throws StateMachineException
     */
    protected function setState($state): void
    {
        if (! in_array($state, $this->config['states'])) {
            throw new StateMachineException(sprintf(
                'Cannot set the state to "%s" to object "%s" because it is not pre-defined.',
                $state,
                get_class($this->object)
            ));
        }

        $property = $this->config['state_property_name'];

        $this->object->$property = $state;

        try {
            $this->object->save();
        }
        catch (\Exception $exception)
        {
            throw new StateMachineException('State can not be set! Class: ' . get_class($this->object));
        }
    }
}
