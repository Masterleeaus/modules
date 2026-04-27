<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integration Registry
    |--------------------------------------------------------------------------
    | All supported integrations. Each entry defines auth type, label, and
    | platform-default credentials (tenants can override with BYO keys).
    */

    'integrations' => [

        // Calendar
        'google_calendar' => [
            'label'       => 'Google Calendar',
            'icon'        => 'google',
            'category'    => 'calendar',
            'auth'        => 'oauth',
            'scopes'      => ['https://www.googleapis.com/auth/calendar'],
            'client_id'   => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
        ],

        'outlook_calendar' => [
            'label'       => 'Outlook / Microsoft 365 Calendar',
            'icon'        => 'microsoft',
            'category'    => 'calendar',
            'auth'        => 'oauth',
            'scopes'      => ['Calendars.ReadWrite', 'offline_access'],
            'client_id'   => env('MICROSOFT_CLIENT_ID'),
            'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
            'redirect_uri' => env('MICROSOFT_REDIRECT_URI'),
            'tenant_id'   => env('MICROSOFT_TENANT_ID', 'common'),
        ],

        'ical_feed' => [
            'label'    => 'Apple Calendar / iCal Feed',
            'icon'     => 'apple',
            'category' => 'calendar',
            'auth'     => 'none',
            'note'     => 'Provides a read-only iCal URL for each cleaner',
        ],

        // Accounting
        'xero' => [
            'label'         => 'Xero',
            'icon'          => 'xero',
            'category'      => 'accounting',
            'auth'          => 'oauth',
            'scopes'        => ['openid', 'profile', 'email', 'accounting.contacts', 'accounting.transactions', 'offline_access'],
            'client_id'     => env('XERO_CLIENT_ID'),
            'client_secret' => env('XERO_CLIENT_SECRET'),
            'redirect_uri'  => env('XERO_REDIRECT_URI'),
        ],

        'quickbooks' => [
            'label'         => 'QuickBooks Online',
            'icon'          => 'quickbooks',
            'category'      => 'accounting',
            'auth'          => 'oauth',
            'client_id'     => env('QUICKBOOKS_CLIENT_ID'),
            'client_secret' => env('QUICKBOOKS_CLIENT_SECRET'),
            'redirect_uri'  => env('QUICKBOOKS_REDIRECT_URI'),
            'sandbox'       => env('QUICKBOOKS_SANDBOX', false),
        ],

        'myob' => [
            'label'         => 'MYOB AccountRight',
            'icon'          => 'myob',
            'category'      => 'accounting',
            'auth'          => 'oauth',
            'client_id'     => env('MYOB_CLIENT_ID'),
            'client_secret' => env('MYOB_CLIENT_SECRET'),
            'redirect_uri'  => env('MYOB_REDIRECT_URI'),
        ],

        // CRM
        'hubspot' => [
            'label'    => 'HubSpot',
            'icon'     => 'hubspot',
            'category' => 'crm',
            'auth'     => 'api_key',
            'api_key'  => env('HUBSPOT_API_KEY'),
        ],

        'salesforce' => [
            'label'         => 'Salesforce',
            'icon'          => 'salesforce',
            'category'      => 'crm',
            'auth'          => 'oauth',
            'client_id'     => env('SALESFORCE_CLIENT_ID'),
            'client_secret' => env('SALESFORCE_CLIENT_SECRET'),
            'redirect_uri'  => env('SALESFORCE_REDIRECT_URI'),
        ],

        'pipedrive' => [
            'label'    => 'Pipedrive',
            'icon'     => 'pipedrive',
            'category' => 'crm',
            'auth'     => 'api_key',
            'api_key'  => env('PIPEDRIVE_API_KEY'),
        ],

        // Marketing
        'mailchimp' => [
            'label'    => 'Mailchimp',
            'icon'     => 'mailchimp',
            'category' => 'marketing',
            'auth'     => 'api_key',
            'api_key'  => env('MAILCHIMP_API_KEY'),
            'list_id'  => env('MAILCHIMP_LIST_ID'),
        ],

        'activecampaign' => [
            'label'    => 'ActiveCampaign',
            'icon'     => 'activecampaign',
            'category' => 'marketing',
            'auth'     => 'api_key',
            'api_key'  => env('ACTIVECAMPAIGN_API_KEY'),
            'api_url'  => env('ACTIVECAMPAIGN_API_URL'),
        ],

        // Communication
        'slack' => [
            'label'           => 'Slack',
            'icon'            => 'slack',
            'category'        => 'communication',
            'auth'            => 'webhook',
            'webhook_url'     => env('SLACK_WEBHOOK_URL'),
        ],

        'google_chat' => [
            'label'       => 'Google Chat',
            'icon'        => 'google',
            'category'    => 'communication',
            'auth'        => 'webhook',
            'webhook_url' => env('GOOGLE_CHAT_WEBHOOK_URL'),
        ],

        'teams' => [
            'label'       => 'Microsoft Teams',
            'icon'        => 'microsoft',
            'category'    => 'communication',
            'auth'        => 'webhook',
            'webhook_url' => env('TEAMS_WEBHOOK_URL'),
        ],

        // Automation
        'zapier' => [
            'label'    => 'Zapier',
            'icon'     => 'zapier',
            'category' => 'automation',
            'auth'     => 'webhook',
            'note'     => 'WorkSuite fires events to your Zapier webhook URL',
        ],

        'make' => [
            'label'    => 'Make (Integromat)',
            'icon'     => 'make',
            'category' => 'automation',
            'auth'     => 'webhook',
            'note'     => 'WorkSuite fires events to your Make webhook URL',
        ],

        // Data
        'google_sheets' => [
            'label'         => 'Google Sheets',
            'icon'          => 'google',
            'category'      => 'data',
            'auth'          => 'oauth',
            'scopes'        => ['https://www.googleapis.com/auth/spreadsheets'],
            'client_id'     => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri'  => env('GOOGLE_REDIRECT_URI'),
        ],

        'airtable' => [
            'label'    => 'Airtable',
            'icon'     => 'airtable',
            'category' => 'data',
            'auth'     => 'api_key',
            'api_key'  => env('AIRTABLE_API_KEY'),
            'base_id'  => env('AIRTABLE_BASE_ID'),
        ],

        // Publishing
        'wordpress' => [
            'label'    => 'WordPress',
            'icon'     => 'wordpress',
            'category' => 'publishing',
            'auth'     => 'api_key',
            'note'     => 'Enter your WordPress site URL and Application Password',
        ],

        'google_business' => [
            'label'         => 'Google Business Profile',
            'icon'          => 'google',
            'category'      => 'publishing',
            'auth'          => 'oauth',
            'scopes'        => ['https://www.googleapis.com/auth/business.manage'],
            'client_id'     => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri'  => env('GOOGLE_REDIRECT_URI'),
        ],

        // Field Service
        'servicem8' => [
            'label'         => 'ServiceM8',
            'icon'          => 'servicem8',
            'category'      => 'field_service',
            'auth'          => 'oauth',
            'client_id'     => env('SERVICEM8_CLIENT_ID'),
            'client_secret' => env('SERVICEM8_CLIENT_SECRET'),
            'redirect_uri'  => env('SERVICEM8_REDIRECT_URI'),
        ],

        'airtasker' => [
            'label'    => 'Airtasker',
            'icon'     => 'airtasker',
            'category' => 'field_service',
            'auth'     => 'api_key',
            'api_key'  => env('AIRTASKER_API_KEY'),
        ],

        'hipages' => [
            'label'    => 'Hipages',
            'icon'     => 'hipages',
            'category' => 'field_service',
            'auth'     => 'api_key',
            'api_key'  => env('HIPAGES_API_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | REST API Settings
    |--------------------------------------------------------------------------
    */
    'api' => [
        'version'      => 'v1',
        'rate_limit'   => env('TITAN_API_RATE_LIMIT', 60),   // requests per minute per token
        'token_expiry' => env('TITAN_API_TOKEN_EXPIRY', 365), // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    */
    'webhooks' => [
        'retry_attempts' => 3,
        'retry_delays'   => [5, 30, 300], // seconds: 5s, 30s, 5min
        'timeout'        => 10,
        'events'         => [
            'booking.created',
            'booking.updated',
            'booking.completed',
            'booking.cancelled',
            'invoice.created',
            'invoice.paid',
            'invoice.overdue',
            'client.created',
            'review.submitted',
            'complaint.opened',
            'provider.assigned',
        ],
    ],
];
