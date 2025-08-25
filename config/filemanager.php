<?php

return [
    // all paths are scoped to this disk
    'disk' => 'public',
    // root inside the disk (OpenCart uses image/catalog)
    'base_dir' => 'catalog',

    // UI page size
    'per_page' => 16,

    // size limit shown in UI (MB) and enforced server-side
    'max_mb' => env('FILEMANAGER_MAX_MB', 10),

    // allowed extensions / mimes (parity with OpenCart)
    'allowed_ext' => ['ico','gif','jpg','jpe','jpeg','png','webp'],
    'allowed_mimes' => [
        'image/x-icon','image/jpeg','image/pjpeg',
        'image/png','image/x-png','image/gif','image/webp',
    ],

    // default thumb size (used by picker)
    'thumb_w' => 300,
    'thumb_h' => 300,
];
