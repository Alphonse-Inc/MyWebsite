-- Portfolio schema for Emmanuel Oluwamuyiwa Afolayan
-- MySQL 8+ / MariaDB 10.3+ · InnoDB · utf8mb4

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(32) NOT NULL DEFAULT 'owner',
    totp_secret VARCHAR(64) NULL,
    totp_enabled TINYINT(1) NOT NULL DEFAULT 0,
    must_change_password TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email),
    KEY users_role_index (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_resets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY password_resets_token_unique (token_hash),
    KEY password_resets_user_index (user_id),
    KEY password_resets_expires_index (expires_at),
    CONSTRAINT password_resets_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS profile (
    id TINYINT UNSIGNED NOT NULL,
    full_name VARCHAR(190) NOT NULL,
    display_name VARCHAR(120) NOT NULL,
    handle VARCHAR(80) NOT NULL,
    titles JSON NOT NULL,
    tagline VARCHAR(500) NOT NULL,
    bio TEXT NOT NULL,
    photo_path VARCHAR(255) NULL,
    photo_alt VARCHAR(255) NULL,
    photo_meta JSON NULL,
    hero_video_path VARCHAR(255) NULL,
    hero_poster_path VARCHAR(255) NULL,
    hero_poster_meta JSON NULL,
    availability_label VARCHAR(120) NOT NULL,
    available TINYINT(1) NOT NULL DEFAULT 1,
    quote TEXT NOT NULL,
    quote_author VARCHAR(120) NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS social_links (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    platform VARCHAR(40) NOT NULL,
    label VARCHAR(80) NOT NULL,
    url VARCHAR(500) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY social_links_sort_index (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(40) NOT NULL DEFAULT 'code',
    sort_order INT NOT NULL DEFAULT 0,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY services_sort_index (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS experiences (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    role VARCHAR(160) NOT NULL,
    organization VARCHAR(160) NOT NULL,
    location VARCHAR(160) NOT NULL DEFAULT '',
    start_label VARCHAR(40) NOT NULL,
    end_label VARCHAR(40) NOT NULL,
    description TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY experiences_sort_index (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS education (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(190) NOT NULL,
    institution VARCHAR(190) NOT NULL,
    location VARCHAR(160) NOT NULL DEFAULT '',
    start_label VARCHAR(40) NOT NULL,
    end_label VARCHAR(40) NOT NULL,
    description TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY education_sort_index (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS skill_groups (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(80) NOT NULL,
    display_type VARCHAR(16) NOT NULL DEFAULT 'bar',
    sort_order INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY skill_groups_sort_index (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS skills (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    group_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    level TINYINT UNSIGNED NULL,
    sort_order INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY skills_group_index (group_id),
    CONSTRAINT skills_group_fk FOREIGN KEY (group_id) REFERENCES skill_groups (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(80) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY categories_name_unique (name),
    UNIQUE KEY categories_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id INT UNSIGNED NULL,
    name VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    short_description VARCHAR(300) NOT NULL,
    long_description TEXT NULL,
    url VARCHAR(500) NOT NULL,
    technologies JSON NULL,
    year SMALLINT UNSIGNED NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'published',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY projects_slug_unique (slug),
    KEY projects_status_index (status),
    KEY projects_featured_index (featured),
    KEY projects_sort_index (sort_order),
    KEY projects_category_index (category_id),
    CONSTRAINT projects_category_fk FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_images (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT UNSIGNED NOT NULL,
    stem VARCHAR(180) NOT NULL,
    alt_text VARCHAR(255) NOT NULL DEFAULT '',
    is_cover TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    width INT UNSIGNED NULL,
    height INT UNSIGNED NULL,
    meta JSON NULL,
    PRIMARY KEY (id),
    KEY project_images_project_sort_index (project_id, sort_order),
    CONSTRAINT project_images_project_fk FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    author_name VARCHAR(120) NOT NULL,
    author_role VARCHAR(160) NOT NULL DEFAULT '',
    quote TEXT NOT NULL,
    photo_path VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_visible TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY testimonials_sort_index (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(190) NOT NULL,
    body TEXT NOT NULL,
    ip VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'unread',
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY messages_status_index (status),
    KEY messages_created_index (created_at),
    KEY messages_email_index (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    setting_key VARCHAR(80) NOT NULL,
    setting_value MEDIUMTEXT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY settings_key_unique (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rate_limits (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    bucket VARCHAR(80) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    hits INT UNSIGNED NOT NULL DEFAULT 1,
    window_start DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY rate_limits_bucket_ip_unique (bucket, ip),
    KEY rate_limits_window_index (window_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(190) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    success TINYINT(1) NOT NULL DEFAULT 0,
    attempted_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY login_attempts_email_time_index (email, attempted_at),
    KEY login_attempts_ip_time_index (ip, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET foreign_key_checks = 1;
