<?php

return [
    'module_alias' => 'clean_quality',
    'module_name' => 'Clean Quality',
    'packages' => [
        'superadmin',
        'company',
    ],
    'roles' => [
        'admin',
        'employee',
    ],
    'permissions' => [
        'view_clean_quality',
        'add_clean_quality',
        'edit_clean_quality',
        'delete_clean_quality',
        'view_quality_control',
        'add_quality_control',
        'edit_quality_control',
        'delete_quality_control',
    ],
    'package_labels' => [
        'clean_quality' => 'Clean Quality',
        'qualitycontrol' => 'Quality Control',
        'quality_control' => 'Quality Control',
    ],
];
