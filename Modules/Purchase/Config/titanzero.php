<?php

return [
    'module' => 'purchase',
    'capabilities' => [
        [
            'key' => 'purchase.help.explain_page',
            'label' => 'Purchase: Explain this page',
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
            'key' => 'purchase.bills.download',
            'label' => 'Purchase: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'bills.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'purchase.bills.send_invoice',
            'label' => 'Purchase: Send Invoice',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'bills.send_invoice',
            'voice_phrases' => [
                'send invoice'
            ]
        ],
        [
            'key' => 'purchase.inventory-files.download',
            'label' => 'Purchase: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'inventory-files.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'purchase.purchase-contacts.apply_quick_action',
            'label' => 'Purchase: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase-contacts.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'purchase.purchase_inventory.add_files',
            'label' => 'Purchase: Add Files',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_inventory.add_files',
            'voice_phrases' => [
                'add files'
            ]
        ],
        [
            'key' => 'purchase.purchase_inventory.adjust_inventory',
            'label' => 'Purchase: Adjust Inventory',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_inventory.adjust_inventory',
            'voice_phrases' => [
                'adjust inventory'
            ]
        ],
        [
            'key' => 'purchase.purchase_inventory.change_status',
            'label' => 'Purchase: Change Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_inventory.change_status',
            'voice_phrases' => [
                'change status'
            ]
        ],
        [
            'key' => 'purchase.purchase_inventory.download',
            'label' => 'Purchase: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_inventory.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'purchase.purchase_inventory.layout',
            'label' => 'Purchase: Layout',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_inventory.layout',
            'voice_phrases' => [
                'layout'
            ]
        ],
        [
            'key' => 'purchase.purchase_order.add_item',
            'label' => 'Purchase: Add Item',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_order.add_item',
            'voice_phrases' => [
                'add item'
            ]
        ],
        [
            'key' => 'purchase.purchase_order.change_status',
            'label' => 'Purchase: Change Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_order.change_status',
            'voice_phrases' => [
                'change status'
            ]
        ],
        [
            'key' => 'purchase.purchase_order.delete_image',
            'label' => 'Purchase: Delete Image',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_order.delete_image',
            'voice_phrases' => [
                'delete image'
            ]
        ],
        [
            'key' => 'purchase.purchase_order.download',
            'label' => 'Purchase: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_order.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'purchase.purchase_order.send_order',
            'label' => 'Purchase: Send Order',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_order.send_order',
            'voice_phrases' => [
                'send order'
            ]
        ],
        [
            'key' => 'purchase.purchase_order.vendor_currency',
            'label' => 'Purchase: Vendor Currency',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_order.vendor_currency',
            'voice_phrases' => [
                'vendor currency'
            ]
        ],
        [
            'key' => 'purchase.purchase_order_file.download',
            'label' => 'Purchase: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_order_file.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'purchase.purchase_order_products',
            'label' => 'Purchase: Purchase Order Products',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_order_products',
            'voice_phrases' => [
                'purchase order products'
            ]
        ],
        [
            'key' => 'purchase.purchase_orders',
            'label' => 'Purchase: Purchase Orders',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_orders',
            'voice_phrases' => [
                'purchase orders'
            ]
        ],
        [
            'key' => 'purchase.purchase_products.add_images',
            'label' => 'Purchase: Add Images',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_products.add_images',
            'voice_phrases' => [
                'add images'
            ]
        ],
        [
            'key' => 'purchase.purchase_products.adjust_inventory',
            'label' => 'Purchase: Adjust Inventory',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_products.adjust_inventory',
            'voice_phrases' => [
                'adjust inventory'
            ]
        ],
        [
            'key' => 'purchase.purchase_products.apply_quick_action',
            'label' => 'Purchase: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_products.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'purchase.purchase_products.change_status',
            'label' => 'Purchase: Change Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_products.change_status',
            'voice_phrases' => [
                'change status'
            ]
        ],
        [
            'key' => 'purchase.purchase_products.layout',
            'label' => 'Purchase: Layout',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_products.layout',
            'voice_phrases' => [
                'layout'
            ]
        ],
        [
            'key' => 'purchase.purchase_products.options',
            'label' => 'Purchase: Options',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_products.options',
            'voice_phrases' => [
                'options'
            ]
        ],
        [
            'key' => 'purchase.purchase_products.store_images',
            'label' => 'Purchase: Store Images',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_products.store_images',
            'voice_phrases' => [
                'store images'
            ]
        ],
        [
            'key' => 'purchase.purchase_products.update_inventory',
            'label' => 'Purchase: Update Inventory',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_products.update_inventory',
            'voice_phrases' => [
                'update inventory'
            ]
        ],
        [
            'key' => 'purchase.purchase_settings.update_prefix',
            'label' => 'Purchase: Update Prefix',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'purchase_settings.update_prefix',
            'voice_phrases' => [
                'update prefix'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.add_bill_item',
            'label' => 'Purchase: Add Bill Item',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.add_bill_item',
            'voice_phrases' => [
                'add bill item'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.add_item',
            'label' => 'Purchase: Add Item',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.add_item',
            'voice_phrases' => [
                'add item'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.apply_bill_credit',
            'label' => 'Purchase: Apply Bill Credit',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.apply_bill_credit',
            'voice_phrases' => [
                'apply bill credit'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.apply_quick_action',
            'label' => 'Purchase: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.apply_to_bill',
            'label' => 'Purchase: Apply To Bill',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.apply_to_bill',
            'voice_phrases' => [
                'apply to bill'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.creates',
            'label' => 'Purchase: Creates',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.creates',
            'voice_phrases' => [
                'creates'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.delete-image',
            'label' => 'Purchase: Delete Image',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.delete-image',
            'voice_phrases' => [
                'delete image'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.download',
            'label' => 'Purchase: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'purchase.vendor-credits.get_bills',
            'label' => 'Purchase: Get Bills',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-credits.get_bills',
            'voice_phrases' => [
                'get bills'
            ]
        ],
        [
            'key' => 'purchase.vendor-notes.apply_quick_action',
            'label' => 'Purchase: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-notes.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'purchase.vendor-notes.show_verified',
            'label' => 'Purchase: Show Verified',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-notes.show_verified',
            'voice_phrases' => [
                'show verified'
            ]
        ],
        [
            'key' => 'purchase.vendor-payments-fetch.fetch_bill',
            'label' => 'Purchase: Fetch Bill',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-payments-fetch.fetch_bill',
            'voice_phrases' => [
                'fetch bill'
            ]
        ],
        [
            'key' => 'purchase.vendor-payments.apply_quick_action',
            'label' => 'Purchase: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-payments.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'purchase.vendor-payments.clearAmount',
            'label' => 'Purchase: Clearamount',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-payments.clearAmount',
            'voice_phrases' => [
                'clearamount'
            ]
        ],
        [
            'key' => 'purchase.vendor-payments.download',
            'label' => 'Purchase: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor-payments.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'purchase.vendor_notes.ask_for_password',
            'label' => 'Purchase: Ask For Password',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor_notes.ask_for_password',
            'voice_phrases' => [
                'ask for password'
            ]
        ],
        [
            'key' => 'purchase.vendor_notes.check_password',
            'label' => 'Purchase: Check Password',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendor_notes.check_password',
            'voice_phrases' => [
                'check password'
            ]
        ],
        [
            'key' => 'purchase.vendors.apply_quick_action',
            'label' => 'Purchase: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'vendors.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ]
    ],
    'go_enabled' => true,
    'zero_enabled' => true
];
