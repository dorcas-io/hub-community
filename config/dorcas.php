<?php
return [
    'transtrak_token' => env('TRANSTRAK_TOKEN', '+WopX4Y4vcGzxW/aeLlBQPdMNmDXidKtfhPjWylkpCc='),
    
    'integrations' => [
        [
            'type' => 'payment',
            'image_url' => cdn('/images/integrations/paystack.png'),
            'name' => 'paystack',
            'display_name' => 'Paystack',
            'description' => 'Integrate Paystack in your invoicing to allow your customers pay directly from their email',
            'configurations' => [
                ['name' => 'public_key', 'label' => 'Public Key', 'value' => ''],
                ['name' => 'private_key', 'label' => 'Secret Key', 'value' => ''],
            ]
        ],
        [
            'type' => 'payment',
            'image_url' => cdn('/images/integrations/rave.jpg'),
            'name' => 'rave',
            'display_name' => 'Dorcas Pay',
            'description' => 'Integrate Dorcas Pay in your store to allow your customers checkout by paying online',
            'configurations' => [
                ['name' => 'public_key', 'label' => 'Public Key', 'value' => ''],
                ['name' => 'private_key', 'label' => 'Secret Key', 'value' => ''],
            ]
        ]
    ],
    'plans' => [
        'starter' => [
            'description' => [
                'short' => '',
                'long' => ''
            ],
            'features' => [
                'BASIC APPS',
                'Dorcas Footer Branding',
                'Chat & Email Support'
            ],
            'footnote' => 'The Starter Package is suitable for small businesses with 1-5 employees, trying out the all-in-one business software suite either as a Business, Professional or Vendor'
        ],
        'classic' => [
            'description' => [
                'short' => 'Get 17% off',
                'long' => 'Get 2 months Off when you pay for 1 year'
            ],
            'features' => [
                'Full Apps',
                'Modules',
                'Messaging Pack',
                'Custom Branding/URL',
                'Phone, Chat & Email Support'
            ],
            'footnote' => 'The Classic Package is suitable for small businesses with up to 25 employees. It will remove branding and unlock powerful tools and features. It will also allow you to switch between Business, Professional & Vendor Modes'
        ],
        'premium' => [
            'description' => [
                'short' => 'Get 17% off',
                'long' => 'Get 2 months Off when you pay for 1 year'
            ],
            'features' => [
                'Full Apps',
                'Modules',
                'Messaging Pack',
                'Custom Branding/URL',
                'Phone, Chat & Email Support',
                'Unlimited Users Access'
            ],
            'footnote' => 'The Premium Package is suitable for more profitable businesses needing more power, features, designation, control and customization.'
        ]
    ],
    "path_messages" => [
        "home" => [
            "upgrade" => "Thanks for using the (FREE) Starter Version of the [PartnerAppName] software. To take your
            business to the next level, consider upgrading to the Classic version for more power"
        ],
        "apps/crm/customers" => [
            "upgrade" => "Hope you are enjoying the Customers Module. Consider upgrading to the Classic version to
            manage more than customers. It will also unlock the Deals Funnel feature"
        ],
        "apps/ecommerce" => [
            "upgrade" => "Thanks for using the eCommerce Module. Consider upgrading to the Classic version to have your
            own custom FREE domain name (e.g. mybusinessabc.com.ng). It will also unlock the full power of the Website
            & Online Store builders."
        ],
        "apps/finance" => [
            "upgrade" => "Thanks for using the Finance Module. Consider upgrading to the Classic version to enable full
            accounting automation. It will unlock TransTrak feature, syncing your banking transactions with your
            accounting system."
        ],
        "apps/people" => [
            "upgrade" => "Thanks for using the People Module. Consider upgrading to the Classic Version to manage more
            employees. It will also unlock the Payroll feature."
        ],
        "apps/inventory" => [
            "upgrade" => "Thanks for using the Sales Module. Consider upgrading to the Classic Version to remove the
            @[[PartnerAppName]] from your invoices. It will unlock the Deals Funnel feature"
        ],
        "directory" => [
            "upgrade" => "Thanks for using the @[[PartnerAppName]] software. Consider upgrading to the Classic Version
            to be able to multi-task and switch between Business, Vendor or Professional modes"
        ],
        "subscription" => [
            "standard" => "If you start a subscription, you will be billed that amount monthly. You can always cancel
            the subscription at ANY TIME to return to the Starter (FREE) version"
        ]
    ]
];