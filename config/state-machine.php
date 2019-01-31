<?php

return [
    // Name of your object
    'order' => [
        // Objects current state property name
        'state_property_name' => 'state',
        // All available states
        'states' => [
           'pending',
           'confirmed',
           'done',
           'canceled',
        ],
        // Transition mapping
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
    ],
];