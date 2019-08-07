<?php

namespace Tests\Unit;

use Exabyssus\StateMachine\StateMachine;
use Exabyssus\StateMachine\Traits\HasStateHistory;
use Exabyssus\StateMachine\Traits\HasStateMachine;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\CreatesApplication;
use Tests\TestCase;

class StateMachineTest extends TestCase
{
    use CreatesApplication;

    protected $stateMachine;

    public function setUp()
    {
        parent::setUp();
        Config::set('state-machine.order', include( __DIR__.'/../../config/state-machine.php');
        $this->stateMachine = new StateMachine(new Order());
    }

    public function testCan(){
        $this->stateMachine->can('confirm');
    }

}

class Order {
    use HasStateMachine, HasStateHistory;


}