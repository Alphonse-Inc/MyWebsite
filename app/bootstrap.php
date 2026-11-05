<?php

declare(strict_types=1);

use App\Core\Env;
use App\Core\Router;
use App\Core\Session;

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

Env::load($root . '/.env');

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', $root . '/storage/logs/php.log');
error_reporting(E_ALL);

$nonce = rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '=');
$GLOBALS['csp_nonce'] = $nonce;

set_exception_handler(static function (Throwable $e) use ($root): void {
    $dir = $root . '/storage/logs';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $line = date('c') . ' ' . $e::class . ' ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n";
    file_put_contents($dir . '/app.log', $line, FILE_APPEND);
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $line);
        exit(1);
    }
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }
    echo fallback_html(500, 'Something went wrong', 'The page could not be loaded. Please try again shortly.');
    exit;
});

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

if (PHP_SAPI !== 'cli') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = is_string($path) ? $path : '/';
    if ($path === '/index.php') {
        header('Location: /', true, 301);
        exit;
    }
    if ($path !== '/' && str_ends_with($path, '/')) {
        $target = rtrim($path, '/');
        $qs = $_SERVER['QUERY_STRING'] ?? '';
        header('Location: ' . $target . ($qs !== '' ? '?' . $qs : ''), true, 301);
        exit;
    }
    Session::start();
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:; media-src 'self'; font-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'nonce-{$nonce}' https://www.googletagmanager.com; frame-src https://www.google.com https://maps.google.com https://www.openstreetmap.org; connect-src 'self' https://www.google-analytics.com https://region1.google-analytics.com; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
    if ($https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    $router = new Router();
    $register = require $root . '/app/routes.php';
    $register($router);
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
}
