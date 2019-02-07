<?php

namespace CubeSystems\StateMachine\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class StateChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $object;
    public $transition;
    public $stateTo;

    /**
     * StateChanged constructor.
     * @param $object
     * @param $transition
     * @param $stateTo
     */
    public function __construct($object, string $transition, string $stateTo)
    {
        $this->object = $object;
        $this->transition = $transition;
        $this->stateTo = $stateTo;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('finite-state');
    }
}
