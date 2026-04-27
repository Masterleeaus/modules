<?php

return {
    'module': 'accountings',
    'capabilities': [
        {
            'key': 'accountings.help.explain_page',
            'label': 'Accountings: Explain this page',
            'risk': 'low',
            'requires': [],
            'handler': 'titanzero.intent.explain_page',
            'voice_phrases': [
                'what is this page',
                'explain this',
                'help me'
            ]
        },
        {
            'key': 'accountings.acc-settings.cashflow',
            'label': 'Accountings: Cashflow',
            'risk': 'low',
            'requires': [],
            'handler': 'acc-settings.cashflow',
            'voice_phrases': [
                'cashflow'
            ]
        },
        {
            'key': 'accountings.acc-settings.cashflow.recurring.add',
            'label': 'Accountings: Add',
            'risk': 'low',
            'requires': [],
            'handler': 'acc-settings.cashflow.recurring.add',
            'voice_phrases': [
                'add'
            ]
        },
        {
            'key': 'accountings.acc-settings.cashflow.recurring.toggle',
            'label': 'Accountings: Toggle',
            'risk': 'low',
            'requires': [],
            'handler': 'acc-settings.cashflow.recurring.toggle',
            'voice_phrases': [
                'toggle'
            ]
        },
        {
            'key': 'accountings.acc-settings.cashflow.save',
            'label': 'Accountings: Save',
            'risk': 'low',
            'requires': [],
            'handler': 'acc-settings.cashflow.save',
            'voice_phrases': [
                'save'
            ]
        },
        {
            'key': 'accountings.acc-settings.index',
            'label': 'Accountings: Index',
            'risk': 'low',
            'requires': [],
            'handler': 'acc-settings.index',
            'voice_phrases': [
                'index'
            ]
        },
        {
            'key': 'accountings.acc-settings.store',
            'label': 'Accountings: Store',
            'risk': 'low',
            'requires': [],
            'handler': 'acc-settings.store',
            'voice_phrases': [
                'store'
            ]
        },
        {
            'key': 'accountings.dashboard',
            'label': 'Accountings: Dashboard',
            'risk': 'low',
            'requires': [],
            'handler': 'accountings.dashboard',
            'voice_phrases': [
                'dashboard'
            ]
        },
        {
            'key': 'accountings.cashflow.ar_aging',
            'label': 'Accountings: Ar Aging',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.ar_aging',
            'voice_phrases': [
                'ar aging'
            ]
        },
        {
            'key': 'accountings.cashflow.collections',
            'label': 'Accountings: Collections',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.collections',
            'voice_phrases': [
                'collections'
            ]
        },
        {
            'key': 'accountings.cashflow.forecast',
            'label': 'Accountings: Forecast',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.forecast',
            'voice_phrases': [
                'forecast'
            ]
        },
        {
            'key': 'accountings.cashflow.index',
            'label': 'Accountings: Index',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.index',
            'voice_phrases': [
                'index'
            ]
        },
        {
            'key': 'accountings.cashflow.payables',
            'label': 'Accountings: Payables',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.payables',
            'voice_phrases': [
                'payables'
            ]
        },
        {
            'key': 'accountings.cashflow.planner',
            'label': 'Accountings: Planner',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.planner',
            'voice_phrases': [
                'planner'
            ]
        },
        {
            'key': 'accountings.cashflow.receivables',
            'label': 'Accountings: Receivables',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.receivables',
            'voice_phrases': [
                'receivables'
            ]
        },
        {
            'key': 'accountings.cashflow.runway',
            'label': 'Accountings: Runway',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.runway',
            'voice_phrases': [
                'runway'
            ]
        },
        {
            'key': 'accountings.cashflow.runway_weekly',
            'label': 'Accountings: Runway Weekly',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.runway_weekly',
            'voice_phrases': [
                'runway weekly'
            ]
        },
        {
            'key': 'accountings.cashflow.top_overdue',
            'label': 'Accountings: Top Overdue',
            'risk': 'low',
            'requires': [],
            'handler': 'cashflow.top_overdue',
            'voice_phrases': [
                'top overdue'
            ]
        },
        {
            'key': 'accountings.journal.apply_quick_action',
            'label': 'Accountings: Apply Quick Action',
            'risk': 'low',
            'requires': [],
            'handler': 'journal.apply_quick_action',
            'voice_phrases': [
                'apply quick action'
            ]
        },
        {
            'key': 'accountings.journal.download',
            'label': 'Accountings: Download',
            'risk': 'low',
            'requires': [],
            'handler': 'journal.download',
            'voice_phrases': [
                'download'
            ]
        }
    ],
    'go_enabled': true,
    'zero_enabled': true
};
