<?php

declare(strict_types=1);

use App\Core\Cache;
use App\Core\Database;
use App\Core\Env;
use App\Core\ImageProcessor;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Setting;

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require dirname(__DIR__) . '/app/bootstrap.php';

$force = in_array('--force', $argv ?? [], true);
$now = date('Y-m-d H:i:s');
$defaults = require dirname(__DIR__) . '/config/site.php';
$pdo = Database::pdo();

$existing = $pdo->query('SELECT COUNT(*) FROM profile')->fetchColumn();
if ((int) $existing > 0 && !$force) {
    fwrite(STDOUT, "Content already exists. Re-run with --force to replace seeded content.\n");
    exit(0);
}

if ($force) {
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (['project_images', 'projects', 'categories', 'skills', 'skill_groups', 'services', 'experiences', 'education', 'social_links', 'testimonials', 'profile', 'settings'] as $table) {
        $pdo->exec('TRUNCATE TABLE `' . $table . '`');
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

$processor = new ImageProcessor();
$uploadRoot = dirname(__DIR__) . '/public_html/uploads';

$pdo->beginTransaction();
try {
    $profile = $defaults['profile'];
    $stmt = $pdo->prepare('INSERT INTO profile (id, full_name, display_name, handle, titles, tagline, bio, photo_alt, availability_label, available, quote, quote_author, updated_at) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $profile['full_name'],
        $profile['display_name'],
        $profile['handle'],
        json_encode($profile['titles'], JSON_UNESCAPED_UNICODE),
        $profile['tagline'],
        $profile['bio'],
        $profile['photo_alt'],
        $profile['availability_label'],
        $profile['available'],
        $profile['quote'],
        $profile['quote_author'],
        $now,
    ]);

    $social = $pdo->prepare('INSERT INTO social_links (platform, label, url, sort_order, is_visible) VALUES (?, ?, ?, ?, 1)');
    foreach ($defaults['socials'] as $row) {
        $social->execute([$row['platform'], $row['label'], $row['url'], $row['sort_order']]);
    }

    $service = $pdo->prepare('INSERT INTO services (name, description, icon, sort_order, is_visible) VALUES (?, ?, ?, ?, 1)');
    foreach ($defaults['services'] as $row) {
        $service->execute([$row['name'], $row['description'], $row['icon'], $row['sort_order']]);
    }

    $experience = $pdo->prepare('INSERT INTO experiences (role, organization, location, start_label, end_label, description, sort_order, is_visible) VALUES (?, ?, ?, ?, ?, ?, ?, 1)');
    foreach ($defaults['experiences'] as $row) {
        $experience->execute([$row['role'], $row['organization'], $row['location'], $row['start_label'], $row['end_label'], $row['description'], $row['sort_order']]);
    }

    $education = $pdo->prepare('INSERT INTO education (title, institution, location, start_label, end_label, description, sort_order, is_visible) VALUES (?, ?, ?, ?, ?, ?, ?, 1)');
    foreach ($defaults['education'] as $row) {
        $education->execute([$row['title'], $row['institution'], $row['location'], $row['start_label'], $row['end_label'], $row['description'], $row['sort_order']]);
    }

    $groupStmt = $pdo->prepare('INSERT INTO skill_groups (name, display_type, sort_order) VALUES (?, ?, ?)');
    $skillStmt = $pdo->prepare('INSERT INTO skills (group_id, name, level, sort_order) VALUES (?, ?, ?, ?)');
    foreach ($defaults['skill_groups'] as $group) {
        $groupStmt->execute([$group['name'], $group['display_type'], $group['sort_order']]);
        $groupId = (int) $pdo->lastInsertId();
        $order = 1;
        foreach ($group['skills'] as $skill) {
            $skillStmt->execute([$groupId, $skill['name'], $skill['level'], $order]);
            $order++;
        }
    }

    $categoryIds = [];
    $categoryStmt = $pdo->prepare('INSERT INTO categories (name, slug, sort_order) VALUES (?, ?, ?)');
    $sort = 1;
    foreach ($defaults['categories'] as $category) {
        $categoryStmt->execute([$category['name'], $category['slug'], $sort]);
        $categoryIds[$category['name']] = (int) $pdo->lastInsertId();
        $sort++;
    }

    $projectStmt = $pdo->prepare('INSERT INTO projects (category_id, name, slug, short_description, long_description, url, technologies, year, status, featured, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, NULL, ?, ?, NULL, ?, 0, ?, ?, ?)');
    $projectIds = [];
    foreach ($defaults['projects'] as $project) {
        $projectStmt->execute([
            $categoryIds[$project['category']] ?? null,
            $project['name'],
            $project['slug'],
            $project['short_description'],
            $project['url'],
            '[]',
            'published',
            $project['sort_order'],
            $now,
            $now,
        ]);
        $projectIds[$project['slug']] = (int) $pdo->lastInsertId();
    }

    $setting = $pdo->prepare('INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, ?)');
    foreach ($defaults['settings'] as $key => $value) {
        $setting->execute([$key, (string) $value, $now]);
    }

    $override = Env::get('SEED_ADMIN_PASSWORD');
    $hash = $override !== null && $override !== ''
        ? password_hash($override, PASSWORD_DEFAULT)
        : '$2y$10$PZHSwh36CFNP3USBAXcEFuF6UqhX7CRjducFvWrSDlQUaPNQHfwQK';
    $user = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $user->execute(['iamemmaafolayan@gmail.com']);
    if ($user->fetch() === false) {
        $insertUser = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, must_change_password, created_at, updated_at) VALUES (?, ?, ?, ?, 1, ?, ?)');
        $insertUser->execute(['Emmanuel Afolayan', 'iamemmaafolayan@gmail.com', $hash, 'owner', $now, $now]);
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

$legacy = 'https://muyiwa-dev.com.ng/';
$profileDir = $uploadRoot . '/profile';
if (!is_dir($profileDir)) {
    mkdir($profileDir, 0755, true);
}

$photo = import_image($processor, $profileDir, $legacy . 'images/profile_pic.png', 'Emmanuel Afolayan');
$poster = import_image($processor, $profileDir, $legacy . 'images/me.jpg', 'Emmanuel Afolayan');
$videoRelative = import_video($legacy . 'images/slider-video.mp4', $profileDir);

$current = Profile::get();
if ($current !== null) {
    Profile::save([
        'full_name' => $current['full_name'],
        'display_name' => $current['display_name'],
        'handle' => $current['handle'],
        'titles' => $current['titles'],
        'tagline' => $current['tagline'],
        'bio' => $current['bio'],
        'photo_path' => 'profile/' . $photo['stem'],
        'photo_alt' => $current['photo_alt'],
        'photo_meta' => $photo['meta'],
        'hero_video_path' => $videoRelative,
        'hero_poster_path' => 'profile/' . $poster['stem'],
        'hero_poster_meta' => $poster['meta'],
        'availability_label' => $current['availability_label'],
        'available' => (int) $current['available'],
        'quote' => $current['quote'],
        'quote_author' => $current['quote_author'],
    ]);
    Setting::put('og_image', '/uploads/profile/' . $photo['stem'] . '-large.jpg');
}

foreach ($defaults['projects'] as $project) {
    $id = $projectIds[$project['slug']];
    $dir = $uploadRoot . '/projects/' . $id;
    $image = import_image($processor, $dir, $legacy . $project['legacy_image'], $project['name']);
    Project::addImage($id, $image['stem'], $project['name'], $image['width'], $image['height'], $image['meta'], true);
}

Cache::flush();
fwrite(STDOUT, "Seed complete. Owner: iamemmaafolayan@gmail.com\n");
fwrite(STDOUT, "Initial password is documented in DEPLOYMENT.md. Change it immediately after the first login.\n");

function import_image(ImageProcessor $processor, string $directory, string $url, string $label): array
{
    $temp = temp_file($url, $label);
    if ($temp !== null) {
        try {
            $stored = $processor->processFile($temp, $directory);
            @unlink($temp);
            fwrite(STDOUT, "Saved image for {$label}\n");
            return $stored;
        } catch (Throwable $e) {
            @unlink($temp);
            fwrite(STDOUT, "Image processing failed for {$label}: {$e->getMessage()}\n");
        }
    } else {
        fwrite(STDOUT, "Download failed for {$label}. Using a labelled placeholder.\n");
    }
    return $processor->placeholder($directory, $label);
}

function import_video(string $url, string $directory): ?string
{
    $binary = http_get($url);
    if ($binary === null || !str_contains(substr($binary, 0, 32), 'ftyp')) {
        fwrite(STDOUT, "Hero video download failed. The hero will use the portrait only.\n");
        return null;
    }
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    $name = 'hero-' . bin2hex(random_bytes(4)) . '.mp4';
    file_put_contents($directory . '/' . $name, $binary);
    fwrite(STDOUT, "Saved hero video\n");
    return 'profile/' . $name;
}

function temp_file(string $url, string $label): ?string
{
    $binary = http_get($url);
    if ($binary === null || strlen($binary) < 32) {
        return null;
    }
    $path = sys_get_temp_dir() . '/ea-' . md5($label . $url) . '.bin';
    file_put_contents($path, $binary);
    return $path;
}

function http_get(string $url): ?string
{
    $ch = curl_init($url);
    if ($ch === false) {
        return null;
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 40,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; PortfolioSeeder/1.0)',
        CURLOPT_HTTPHEADER => ['Accept: */*'],
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    if (!is_string($body) || $code < 200 || $code >= 300) {
        return null;
    }
    if (str_starts_with(ltrim($body), '<')) {
        return null;
    }
    return $body;
}
