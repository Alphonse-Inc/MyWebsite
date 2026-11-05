<?php

declare(strict_types=1);

use App\Core\CSRF;
use App\Core\View;

function project_root(): string
{
    return dirname(__DIR__, 2);
}

/** @return array<string, mixed> */
function config(string $name): array
{
    static $cache = [];
    if (!isset($cache[$name])) {
        $file = project_root() . '/config/' . $name . '.php';
        $loaded = is_file($file) ? require $file : [];
        $cache[$name] = is_array($loaded) ? $loaded : [];
    }
    return $cache[$name];
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_url(): string
{
    $configured = \App\Core\Env::get('APP_URL');
    if (is_string($configured) && $configured !== '') {
        return rtrim($configured, '/');
    }
    $fallback = config('app')['url'] ?? 'https://muyiwa-dev.com.ng';
    return rtrim((string) $fallback, '/');
}

function url(string $path = '/'): string
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    $path = '/' . ltrim($path, '/');
    return app_url() . ($path === '/' ? '/' : $path);
}

function asset(string $path): string
{
    $path = '/' . ltrim($path, '/');
    $file = project_root() . '/public_html' . $path;
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return $path . '?v=' . $version;
}

function redirect(string $path): never
{
    if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
        $path = $path === '' ? '/' : $path;
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
    }
    header('Location: ' . $path);
    exit;
}

function wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return str_contains((string) $accept, 'application/json')
        || strcasecmp((string) $xhr, 'XMLHttpRequest') === 0;
}

function json_out(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(CSRF::token()) . '">';
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return is_string($ip) && strlen($ip) <= 45 ? $ip : '0.0.0.0';
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = str_replace('&', ' and ', $text);
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    if (is_string($ascii) && $ascii !== '') {
        $text = $ascii;
    }
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text !== '' ? $text : 'item';
}

function clean_text(string $value, int $max): string
{
    $value = str_replace(["\r\n", "\r"], "\n", $value);
    $value = trim(strip_tags($value));
    if (mb_strlen($value) > $max) {
        $value = mb_substr($value, 0, $max);
    }
    return $value;
}

function single_line(string $value, int $max): string
{
    $value = preg_replace('/\s+/', ' ', trim(strip_tags($value))) ?? '';
    if (mb_strlen($value) > $max) {
        $value = mb_substr($value, 0, $max);
    }
    return $value;
}

/** @param array<string, mixed> $data */
function partial(string $template, array $data = []): void
{
    View::partial($template, $data);
}

function icon(string $name, string $class = 'h-5 w-5'): string
{
    $name = strtolower($name);
    if (preg_match('/^[a-z0-9-]+$/', $name) !== 1) {
        return '';
    }
    $file = project_root() . '/public_html/assets/icons/' . $name . '.svg';
    if (!is_file($file)) {
        return '';
    }
    $svg = file_get_contents($file);
    if ($svg === false) {
        return '';
    }
    $svg = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $svg) ?? $svg;
    $svg = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $svg) ?? $svg;
    $classAttr = ' class="' . e($class) . '" aria-hidden="true" focusable="false"';
    if (preg_match('/<svg\b[^>]*>/', $svg) === 1) {
        $svg = preg_replace('/<svg\b([^>]*)>/', '<svg$1' . $classAttr . '>', $svg, 1) ?? $svg;
        $svg = preg_replace('/\s(width|height)="[^"]*"/', '', $svg) ?? $svg;
    }
    return $svg;
}

function setting(array $settings, string $key, string $default = ''): string
{
    $value = $settings[$key] ?? null;
    if ($value === null || $value === '') {
        return $default;
    }
    return (string) $value;
}

/** @param array<string, int> $widths */
function picture(string $base, array $widths, string $alt, string $class = '', string $sizes = '(max-width: 768px) 100vw, 50vw', ?string $blur = null, bool $eager = false): void
{
    $order = ['thumb', 'medium', 'large'];
    $webp = [];
    $jpg = [];
    foreach ($order as $name) {
        if (!isset($widths[$name])) {
            continue;
        }
        $w = (int) $widths[$name];
        $webp[] = e($base . '-' . $name . '.webp') . ' ' . $w . 'w';
        $jpg[] = e($base . '-' . $name . '.jpg') . ' ' . $w . 'w';
    }
    if ($jpg === []) {
        echo '<div class="grid aspect-[4/3] place-items-center rounded-2xl bg-surface2 text-sm text-muted">' . e($alt) . '</div>';
        return;
    }
    $fallback = $base . '-medium.jpg';
    if (!isset($widths['medium'])) {
        $first = array_key_first($widths);
        $fallback = $base . '-' . $first . '.jpg';
    }
    $style = $blur ? ' style="background-image:url(' . e($blur) . ');background-size:cover;background-position:center"' : '';
    echo '<picture>';
    echo '<source type="image/webp" srcset="' . implode(', ', $webp) . '" sizes="' . e($sizes) . '">';
    echo '<img src="' . e($fallback) . '" srcset="' . implode(', ', $jpg) . '" sizes="' . e($sizes) . '" alt="' . e($alt) . '" class="' . e($class) . '" loading="' . ($eager ? 'eager' : 'lazy') . '" decoding="async"' . $style . '>';
    echo '</picture>';
}

function fallback_html(int $code, string $title, string $body): string
{
    http_response_code($code);
    return '<!DOCTYPE html><html lang="en" class="dark"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'
        . e($title)
        . '</title><style>body{margin:0;min-height:100vh;display:grid;place-items:center;background:#070B0A;color:#E8F1EE;font-family:Inter,system-ui,sans-serif}main{max-width:36rem;padding:2rem}a{color:#34D399}</style></head><body><main><p style="font-family:ui-monospace,monospace;letter-spacing:.2em;color:#10B981">EA</p><h1>'
        . e($title)
        . '</h1><p>'
        . e($body)
        . '</p><p><a href="/">Back home</a></p></main></body></html>';
}

function csp_nonce(): string
{
    return (string) ($GLOBALS['csp_nonce'] ?? '');
}

function selected_nav(string $active, string $name): string
{
    return $active === $name ? ' is-active' : '';
}

/** @param list<string> $parts
 *  @return list<string>
 */
function tag_list(string $value): array
{
    $parts = preg_split('/\s*,\s*/', $value) ?: [];
    $out = [];
    foreach ($parts as $part) {
        $part = single_line($part, 40);
        if ($part !== '') {
            $out[] = $part;
        }
    }
    return array_values(array_unique($out));
}

/** @param array<string, mixed> $data */
function person_schema(array $data): array
{
    $profile = is_array($data['profile'] ?? null) ? $data['profile'] : [];
    $settings = is_array($data['settings'] ?? null) ? $data['settings'] : [];
    $same = [];
    foreach (($data['socials'] ?? []) as $social) {
        if (is_array($social) && !empty($social['url'])) {
            $same[] = (string) $social['url'];
        }
    }
    $location = (string) ($settings['contact_location'] ?? 'Lagos, Nigeria');
    return [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => (string) ($profile['full_name'] ?? ''),
        'alternateName' => (string) ($profile['display_name'] ?? ''),
        'description' => (string) ($profile['bio'] ?? ''),
        'jobTitle' => $profile['titles'] ?? [],
        'email' => (string) ($settings['contact_email'] ?? ''),
        'telephone' => (string) ($settings['contact_phone'] ?? ''),
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $location,
        ],
        'url' => url('/'),
        'sameAs' => $same,
    ];
}

function old_value(array $old, string $key, string $fallback = ''): string
{
    $value = $old[$key] ?? $fallback;
    return is_string($value) ? $value : $fallback;
}

function render_project_image(array $image, int $projectId, string $alt, string $class = '', bool $eager = false, string $sizes = '(max-width: 768px) 100vw, 33vw'): void
{
    $meta = is_array($image['meta'] ?? null) ? $image['meta'] : [];
    $widths = is_array($meta['widths'] ?? null) ? $meta['widths'] : [];
    $blur = is_string($meta['blur'] ?? null) ? $meta['blur'] : null;
    picture(\App\Services\Media::baseUrl($projectId, (string) $image['stem']), $widths, $alt !== '' ? $alt : 'Project image', $class, $sizes, $blur, $eager);
}

function render_upload(string $relativeStem, ?array $meta, string $alt, string $class = '', bool $eager = false): void
{
    $widths = is_array($meta['widths'] ?? null) ? $meta['widths'] : [];
    $blur = is_string($meta['blur'] ?? null) ? $meta['blur'] : null;
    picture('/uploads/' . ltrim($relativeStem, '/'), $widths, $alt, $class, '(max-width: 768px) 80vw, 420px', $blur, $eager);
}
