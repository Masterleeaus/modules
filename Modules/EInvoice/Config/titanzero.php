<?php

return [
    'module' => 'einvoice',
    'capabilities' => [
        [
            'key' => 'einvoice.help.explain_page',
            'label' => 'EInvoice: Explain this page',
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
            'key' => 'einvoice.client_modal',
            'label' => 'EInvoice: Client Modal',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'client_modal',
            'voice_phrases' => [
                'client modal'
            ]
        ],
        [
            'key' => 'einvoice.client_save',
            'label' => 'EInvoice: Client Save',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'client_save',
            'voice_phrases' => [
                'client save'
            ]
        ],
        [
            'key' => 'einvoice.settings',
            'label' => 'EInvoice: Settings',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'einvoice.settings',
            'voice_phrases' => [
                'settings'
            ]
        ],
        [
            'key' => 'einvoice.settings_modal',
            'label' => 'EInvoice: Settings Modal',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'einvoice.settings_modal',
            'voice_phrases' => [
                'settings modal'
            ]
        ],
        [
            'key' => 'einvoice.exportXml',
            'label' => 'EInvoice: Exportxml',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'exportXml',
            'voice_phrases' => [
                'exportxml'
            ]
        ],
        [
            'key' => 'einvoice.index',
            'label' => 'EInvoice: Index',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'index',
            'voice_phrases' => [
                'index'
            ]
        ],
        [
            'key' => 'einvoice.settings.save',
            'label' => 'EInvoice: Save',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'settings.save',
            'voice_phrases' => [
                'save'
            ]
        ]
    ],
    'go_enabled' => true,
    'zero_enabled' => true
];
