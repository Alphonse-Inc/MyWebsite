<?php

declare(strict_types=1);

/**
 * Router for the PHP built-in server only.
 * Production uses Apache and public_html/.htaccess.
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$file = __DIR__ . $uri;
if ($uri !== '/' && is_file($file)) {
    return false;
}
require __DIR__ . '/index.php';
