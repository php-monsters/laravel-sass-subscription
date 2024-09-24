<?php

return [
    'database' => [
        'cancel_migrations_autoloading' => false,
    ],

    'feature_tickets' => env('SASSCRIPTION_FEATURE_TICKETS', false),

    'models' => [

        'feature' => \PhpMonsters\Sasscription\Models\Feature::class,

        'feature_consumption' => \PhpMonsters\Sasscription\Models\FeatureConsumption::class,

        'feature_ticket' => \PhpMonsters\Sasscription\Models\FeatureTicket::class,

        'feature_plan' => \PhpMonsters\Sasscription\Models\FeaturePlan::class,

        'plan' => \PhpMonsters\Sasscription\Models\Plan::class,

        'subscriber' => [
            'uses_uuid' => env('SASSCRIPTION_SUBSCRIBER_USES_UUID', false),
        ],

        'subscription' => \PhpMonsters\Sasscription\Models\Subscription::class,

        'subscription_renewal' => \PhpMonsters\Sasscription\Models\SubscriptionRenewal::class,
    ],
];
