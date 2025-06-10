<?php

return [
    'show_warnings' => true,
    'orientation' => 'portrait',
    'defines' => [
        'font_dir' => storage_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => realpath(base_path()),
        'allowed_protocols' => [
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        'log_output_file' => storage_path('logs/dompdf.log'),
        'debug_css' => true,
        'debug_keep_temp' => true,
        'debug_layout' => true,
        'debug_layout_lines' => true,
        'debug_layout_blocks' => true,
        'debug_layout_inline' => true,
        'debug_layout_padding_box' => true,
    ],
    'options' => [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => true,
        'defaultFont' => 'sans-serif',
        'dpi' => 150,
    ],
];