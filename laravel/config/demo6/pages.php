<?php

return [
    '' => [
        'title' => 'Dashboard',
        'description' => '#XRS-45670',
        'view' => 'index',
        'layout' => [
            'page-title' => [
                'description' => true,
                'breadcrumb' => false,
            ],
        ],
        'assets' => [
            'vendors' => [
                'css' => [
                    'plugins/custom/fullcalendar/fullcalendar.bundle.css',
                ],
                'js' => [
                    'plugins/custom/fullcalendar/fullcalendar.bundle.js',
                ],
            ],
        ],
    ],

    'dashboards' => [
        'compact' => [
            'title' => 'Compact',
            'view' => 'index',
            'layout' => [
                'header' => [
                    'left' => 'page-title',
                ],
                'page-title' => [
                    'description' => false,
                    'breadcrumb' => true,
                ],
                'toolbar' => [
                    'display' => true,
                ],
            ],
            'assets' => [
                'vendors' => [
                    'css' => [
                        'plugins/custom/fullcalendar/fullcalendar.bundle.css',
                    ],
                    'js' => [
                        'plugins/custom/fullcalendar/fullcalendar.bundle.js',
                    ],
                ],
            ],
        ],
        'minimal' => [
            'title' => 'Minimal',
            'view' => 'index',
            'layout' => [
                'header' => [
                    'left' => 'page-title',
                ],
                'toolbar' => [
                    'display' => false,
                ],
                'page-title' => [
                    'description' => false,
                    'breadcrumb' => true,
                ],
            ],
            'assets' => [
                'vendors' => [
                    'css' => [
                        'plugins/custom/fullcalendar/fullcalendar.bundle.css',
                    ],
                    'js' => [
                        'plugins/custom/fullcalendar/fullcalendar.bundle.js',
                    ],
                ],
            ],
        ],
        'header' => null,
    ],

    'layout-builder' => [
        'title' => 'Layout Builder',
        'description' => 'Real-time layout options preview and export',
        'view' => 'layout-builder',
        'layout' => [
            'page-title' => [
                'breadcrumb' => false, // hide breadcrumb
            ],
        ],
        'assets' => [
            'custom' => [
                'js' => [
                    'js/custom/layout-builder/layout-builder.js',
                ],
            ],
        ],
    ],
];
