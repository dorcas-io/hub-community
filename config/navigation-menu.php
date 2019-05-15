<?php
return [
    'modules-dashboard-business' => [
        'icon' => 'fa fa-home',
        'dashboard' => 'business',
        'title' => 'Home',
        'route' => 'dashboard',
        'clickable' => true,
        'navbar' => true,
        'sub-menu' => []
    ],
    'modules-assistant' => [
        'icon' => 'fa fa-bar-chart',
        'dashboard' => 'business',
        'title' => 'Assistant',
        'route' => 'assistant',
        'clickable' => false,
        'navbar' => false,
        'sub-menu' => []
    ],
    'modules-customers' => [
        'icon' => 'fa fa-group',
        'dashboard' => 'business',
        'title' => 'Customers',
        'route' => 'customers',
        'clickable' => false,
        'navbar' => true,
        'sub-menu' => []
    ],
    'modules-ecommerce' => [
        'icon' => 'fa fa-desktop',
        'dashboard' => 'business',
        'title' => 'eCommerce',
        'route' => 'e-commerce',
        'clickable' => false,
        'navbar' => true,
        'sub-menu' => []
    ],
    'modules-people' => [
        'icon' => 'fa fa-briefcase',
        'dashboard' => 'business',
        'title' => 'People',
        'route' => 'people',
        'clickable' => false,
        'navbar' => true,
        'sub-menu' => []
    ],
    'modules-finance' => [
        'icon' => 'fa fa-money',
        'dashboard' => 'business',
        'title' => 'Finance',
        'route' => 'finance',
        'clickable' => false,
        'navbar' => true,
        'sub-menu' => []
    ],
    'modules-sales' => [
        'icon' => 'fa fa-bar-chart',
        'dashboard' => 'business',
        'title' => 'Sales',
        'route' => 'sales',
        'clickable' => false,
        'navbar' => true,
        'sub-menu' => []
    ],
    'addons' => [
        'icon' => 'fa fa-laptop',
        'dashboard' => 'business',
        'title' => 'Addons',
        'route' => 'addons',
        'clickable' => false,
        'navbar' => true,
        'sub-menu' => [
            'modules-marketplace' => [
                'title' => 'Marketplace',
                'route' => 'marketplace-main',
                'icon' => 'fa fa-handshake-o',
                'sub-menu' => []
            ],
            'modules-library' => [
                'title' => 'Library',
                'route' => 'library-main',
                'icon' => 'fa fa-book',
                'sub-menu' => []
            ],
            'modules-app-store' => [
                'title' => 'Apps Store',
                'route' => 'app-store-main',
                'icon' => 'fa fa-gift',
                'sub-menu' => []
            ],
            'modules-integration' => [
                'title' => 'Integrations',
                'route' => 'integrations-main',
                'icon' => 'fa fa-gears',
                'sub-menu' => []
            ]
        ]
    ],
    'modules-settings' => [
        'icon' => 'fe fe-settings',
        'dashboard' => 'all',
        'title' => 'Settings',
        'route' => 'settings',
        'clickable' => false,
        'navbar' => true,
        'sub-menu' => []
    ]
];