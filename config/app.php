<?php

declare(strict_types=1);

return [
    'session_name' => 'ea_portfolio',
    'session_idle' => 1800,
    'login_max_attempts' => 5,
    'login_window' => 900,
    'reset_ttl' => 3600,
    'contact_max_per_hour' => 5,
    'upload_max_bytes' => 8 * 1024 * 1024,
    'upload_max_images' => 12,
    'backup_keep' => 5,
];
