<?php

return [
    // Storage disk to use (must be configured in filesystems.php)
    'disk' => env('FILEMANAGER_DISK', 'public'),

    // Base directory inside the disk where images are stored
    // Mirrors OpenCart's image/catalog structure: e.g., 'catalog'
    'base' => env('FILEMANAGER_BASE', 'catalog'),

    // Thumbnail/cache directory INSIDE the same disk, but OUTSIDE of the base directory
    // e.g. if you want thumbs at storage/app/public/cache instead of storage/app/public/catalog/cache
    'cache' => env('FILEMANAGER_CACHE', 'cache'),

    // Allowed file extensions and mimes for uploads
    'allowed_extensions' => ['ico', 'gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp'],
    'allowed_mimes' => [
        'image/x-icon',
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/gif',
        'image/webp',
    ],

    // Max upload size in KB per file
    'max_upload_kb' => env('FILEMANAGER_MAX_UPLOAD_KB', 4096), // 4MB

    // List page size
    'limit' => 12,

    // Default thumb size used by the list endpoint via ThumbController
    'thumb_size' => env('FILEMANAGER_THUMB_SIZE', '300x300'),
];
