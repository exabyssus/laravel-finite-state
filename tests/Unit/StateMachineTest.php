<?php

namespace Tests\Unit;

use Exabyssus\StateMachine\StateMachine;
use Exabyssus\StateMachine\Traits\HasStateHistory;
use Exabyssus\StateMachine\Traits\HasStateMachine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\CreatesApplication;
use Tests\TestCase;
use Mockery;

/**
 * Class StateMachineTest
 * @package Tests\Unit
 */
class StateMachineTest extends TestCase
{
    use CreatesApplication;

    protected const STATE_CONFIG = [
        'order' => [
            'state_property_name' => 'state',
            'states' => [
                'pending',
                'confirmed',
                'done',
                'canceled',
            ],
            'transitions' => [
                'confirm' => [
                    'from' => ['pending'],
                    'to' => 'confirmed'
                ],
                'complete' => [
                    'from' => ['confirmed'],
                    'to' => 'done'
                ],
                'cancel' => [
                    'from' => ['pending', 'confirmed'],
                    'to' => 'canceled'
                ],
            ]
        ]
    ];

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        config()->set('state-machine', self::STATE_CONFIG);
    }

    /**
     * @return void
     */
    public function testCan(): void
    {
        $stateMachine = new StateMachine(new Order());
        $can = $stateMachine->can('confirm');
        $this->assertTrue($can);
    }

    /**
     * @return void
     */
    public function testGetState(): void
    {
        $stateMachine = new StateMachine(new Order());
        $state = $stateMachine->getState();
        $this->assertEquals('pending', $state);
    }

    /**
     * @return void
     */
    public function testGetPossibleTransitions(): void
    {
        $stateMachine = new StateMachine(new Order());
        $possible = $stateMachine->getPossibleTransitions();
        $this->assertIsArray($possible);
        $possible = array_values($possible);
        $this->assertEquals(['confirm', 'cancel'], $possible);
    }

    /**
     * @return void
     */
    public function testTransitionAllowed(): void
    {
        $model = new Order();
        $this->assertTrue($model->transitionAllowed('confirm'));
        $this->assertFalse($model->transitionAllowed('complete'));
    }

    /**
     * @return void
     */
    public function testApply(): void
    {
        $model = new Order();
        $stateMachine = new StateMachine($model);
        $stateMachine->apply('confirm');
        $this->assertEquals('confirmed', $model->state);
    }

    /**
     * @return void
     */
    public function testFindHistoricalStatesByTo(): void
    {
        $model = new Order();
        $stateMachine = new StateMachine($model);
        $stateMachine->apply('confirm');
        $this->assertEquals('confirmed', $model->state);

        $historyLine = $model->findHistoricalStatesByTo('confirmed')->first();

        $this->assertEquals($historyLine->owner_type, Order::class);
        $this->assertEquals(1, $historyLine->owner_id);
        $this->assertEquals('confirm', $historyLine->transition);
    }

    /**
     * @return void
     */
    public function testFindHistoricalStatesByTransition(): void
    {
        $model = new Order();
        $stateMachine = new StateMachine($model);
        $stateMachine->apply('confirm');
        $this->assertEquals('confirmed', $model->state);

        $historyLine = $model->findHistoricalStatesByTransition('confirm')->first();

        $this->assertEquals($historyLine->owner_type, Order::class);
        $this->assertEquals(1, $historyLine->owner_id);
        $this->assertEquals('confirm', $historyLine->transition);
    }

    /**
     * @return void
     */
    public function testHasStateInHistory(): void
    {
        $model = new Order();
        $stateMachine = new StateMachine($model);
        $stateMachine->apply('confirm');
        $this->assertEquals('confirmed', $model->state);

        $this->assertTrue($model->hasStateInHistory('confirmed'));
        $this->assertFalse($model->hasStateInHistory('canceled'));
    }

    /**
     * @return void
     */
    public function testHasTransitionInHistory(): void
    {
        $model = new Order();
        $stateMachine = new StateMachine($model);
        $stateMachine->apply('confirm');
        $this->assertEquals('confirmed', $model->state);

        $this->assertTrue($model->hasTransitionInHistory('confirm'));
        $this->assertFalse($model->hasTransitionInHistory('cancel'));
    }
}

class Order extends Model
{
    use HasStateMachine, HasStateHistory;

    /**
     * Order constructor.
     * @param string $state
     */
    public function __construct(string $state = 'pending')
    {
        $this->state = $state;
        $this->id = 1;
    }

    public function save(array $options = [])
    {
        return true;
    }
}