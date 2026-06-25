<?php

declare(strict_types=1);

return [
    'max_upload_mb' => (int) env('APK_MAX_UPLOAD_MB', 250),
    'storage_disk' => (string) env('APK_STORAGE_DISK', 'apks'),
    'trusted_proxy_ips' => array_filter(array_map(
        static fn (string $value): string => trim($value),
        explode(',', (string) env('TRUSTED_PROXY_IPS', '127.0.0.1')),
    )),
];
