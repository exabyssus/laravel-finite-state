<?php

namespace Exabyssus\StateMachine\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class StateChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $object;
    private $transition;

    /**
     * StateChanged constructor.
     * @param $object
     * @param $transition
     */
    public function __construct($object, $transition)
    {
        $this->object = $object;
        $this->transition = $transition;
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
