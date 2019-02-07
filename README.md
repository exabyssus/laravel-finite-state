# Laravel Finite State

[![Latest Stable Version](https://poser.pugx.org/exabyssus/laravel-finite-state/v/stable)](https://packagist.org/packages/exabyssus/laravel-finite-state)
[![Latest Unstable Version](https://poser.pugx.org/exabyssus/laravel-finite-state/v/unstable)](https://packagist.org/packages/exabyssus/laravel-finite-state)
[![Total Downloads](https://poser.pugx.org/exabyssus/laravel-finite-state/downloads)](https://packagist.org/packages/exabyssus/laravel-finite-state)
[![License](https://poser.pugx.org/exabyssus/laravel-finite-state/license)](https://packagist.org/packages/exabyssus/laravel-finite-state)

Package gives an easy way to add StateMachine to your Eloquent Models.

StateMachine helps you control state flow and records state history. 

# Installation

Add package to your composer.

```
composer require exabyssus/laravel-finite-state
```

Publish package to copy config and migration files.

```
php artisan vendor:publish --provider="Exabyssus\StateMachine\StateMachineServiceProvider"   
```

Add Traits to Models you want to add State Machine

```
use Exabyssus\StateMachine\Traits\HasStateHistory;
use Exabyssus\StateMachine\Traits\HasStateMachine;

class YourModel extends Model
{
    use HasStateMachine;
    use HasStateHistory;
    ...
}

```

Configure State Machine transitions

```
config/state-machine.php
```

# Configuration

```
<?php

return [
    'order' => [  // Name of your object
        'state_property_name' => 'state',  // Objects current state property name
        'states' => [  // All available states
           'pending',
           'confirmed',
        ],
        'transitions' => [  // Transition mapping
            'confirm' => [
                'from' => ['pending'],
                'to' => 'confirmed'
            ],
            'cancel' => [
                'from' => ['pending', 'confirmed'],
                'to' => 'canceled'
            ],
        ]
    ],
];
```


# Usage 

Check if transition is allow for Object

```
transitionAllowed (strign $status): bool

```

Apply transition 

```
transition(string $transition): void

```


Get available transitions  

```
getPossibleTransitions(): array

```

# Events

To catch Object before state is change add __afterStateChange__ method to your class.

```
function beforeStateChange($transition, $stateFrom, $stateTo)
```

If function returns `false` state won't be changed.


After state is changed `afterStateChange` method is called.

```
function afterStateChange($transition, $stateTo)
```
And `StateChanged` event is dispatched.
