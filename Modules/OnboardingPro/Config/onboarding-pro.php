<?php
return [
    'name' => 'OnboardingPro',

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'banners'    => true,
        'surveys'    => true,
        'wizard'     => true,
        'tooltips'   => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Completion Criteria
    | Options: 'banners_dismissed', 'survey_submitted', 'both'
    |--------------------------------------------------------------------------
    */
    'completion_criteria' => 'both',

    /*
    |--------------------------------------------------------------------------
    | Step Sequences per role
    | Each step: ['type' => 'banner'|'survey', 'id' => null (resolve at runtime)]
    |--------------------------------------------------------------------------
    */
    'flows' => [
        'admin' => [
            ['type' => 'survey',  'trigger' => 'first_login'],
            ['type' => 'banners', 'role'    => 'admin'],
        ],
        'employee' => [
            ['type' => 'banners', 'role' => 'employee'],
        ],
        'client' => [
            ['type' => 'survey',  'trigger' => 'first_login'],
            ['type' => 'banners', 'role'    => 'client'],
        ],
        'default' => [
            ['type' => 'banners', 'role' => 'all'],
        ],
    ],
];
