<?php

return [
    'module' => 'biometric',
    'capabilities' => [
        [
            'key' => 'biometric.help.explain_page',
            'label' => 'Biometric: Explain this page',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'titanzero.intent.explain_page',
            'voice_phrases' => [
                'what is this page',
                'explain this',
                'help me'
            ]
        ],
        [
            'key' => 'biometric.biometric-devices.change-status',
            'label' => 'Biometric: Change Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'biometric-devices.change-status',
            'voice_phrases' => [
                'change status'
            ]
        ],
        [
            'key' => 'biometric.biometric-devices.commands',
            'label' => 'Biometric: Commands',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'biometric-devices.commands',
            'voice_phrases' => [
                'commands'
            ]
        ],
        [
            'key' => 'biometric.biometric-devices.sync-employees',
            'label' => 'Biometric: Sync Employees',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'biometric-devices.sync-employees',
            'voice_phrases' => [
                'sync employees'
            ]
        ],
        [
            'key' => 'biometric.biometric-employees.fetch-all',
            'label' => 'Biometric: Fetch All',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'biometric-employees.fetch-all',
            'voice_phrases' => [
                'fetch all'
            ]
        ],
        [
            'key' => 'biometric.biometric-employees.fetch-biometric-data',
            'label' => 'Biometric: Fetch Biometric Data',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'biometric-employees.fetch-biometric-data',
            'voice_phrases' => [
                'fetch biometric data'
            ]
        ],
        [
            'key' => 'biometric.biometric-employees.get-employees-to-sync',
            'label' => 'Biometric: Get Employees To Sync',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'biometric-employees.get-employees-to-sync',
            'voice_phrases' => [
                'get employees to sync'
            ]
        ],
        [
            'key' => 'biometric.biometric-employees.get-info',
            'label' => 'Biometric: Get Info',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'biometric-employees.get-info',
            'voice_phrases' => [
                'get info'
            ]
        ],
        [
            'key' => 'biometric.biometric-employees.remove-from-device',
            'label' => 'Biometric: Remove From Device',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'biometric-employees.remove-from-device',
            'voice_phrases' => [
                'remove from device'
            ]
        ],
        [
            'key' => 'biometric.get-biometric-attendance',
            'label' => 'Biometric: Get Biometric Attendance',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'get-biometric-attendance',
            'voice_phrases' => [
                'get biometric attendance'
            ]
        ]
    ],
    'go_enabled' => true,
    'zero_enabled' => true
];
