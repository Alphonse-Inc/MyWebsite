<?php

declare(strict_types=1);

$bootstrap = dirname(__DIR__) . '/app/bootstrap.php';
if (!is_file($bootstrap)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "app/bootstrap.php was not found beside this public_html directory.\n";
    echo "On cPanel, put app, config, storage, database, vendor, and .env in the account home, next to public_html. See HOSTING.md.\n";
    exit;
}

require $bootstrap;
