<?php

return [
    '' => [
        'title' => 'Dashboard',
        'description' => '#XRS-45670',
        'view' => 'index',
        'layout' => [
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
            'layout' => [
                'js' => [
                    'js/layout/toolbar.js',
                ],
            ],
        ],
    ],
];
