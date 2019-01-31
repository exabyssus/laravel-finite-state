<?php

namespace Exabyssus\StateMachine\Models;

use Arbory\Base\Auth\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Order
 * @package App\Models
 * @property Customer|null $customer
 */
class StateTransition extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'owner_class',
        'owner_id',
        'transition',
        'to',
        'user_id',
    ];
}
