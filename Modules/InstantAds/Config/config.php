<?php

return [
    'name'              => 'InstantAds',
    'guest_daily_limit' => 3,
    'max_batch_size'    => 4,
    'titan'             => [
        'enabled' => true,
        'service' => 'Modules\\TitanZero\\Services\\ZeroGateway',
    ],
];
