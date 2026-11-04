# Hosting on cPanel

Production site: `https://muyiwa-dev.com.ng` (no `www`).

The account document root stays `public_html`. There is no Node process, Docker service, or server-side build. Tailwind is already compiled to `public_html/assets/css/app.css`. `vendor/` is already in the repository (PHPMailer). Do not run Composer on the server unless you prefer to rebuild it.

Canonical URLs, Open Graph, the sitemap, and `robots.txt` use `APP_URL`. If that variable is missing, the fallback is `https://muyiwa-dev.com.ng`. `theme-color` stays `#070B0A`.

## Layout that cPanel actually serves

PHP looks one directory above `public_html` for the application. On a normal cPanel account that parent is the home directory, which File Manager and the main FTP account can see, and which PHP is allowed to read. Those folders are not web URLs.

```
/home/USERNAME/
  .env
  app/
  config/
  database/
  storage/
  vendor/
  composer.json
  composer.lock
  public_html/              ← cPanel document root
    index.php
    .htaccess
    .user.ini
    assets/
    uploads/
```

Upload the **contents** of this repository's `public_html/` into the account `public_html/`. Upload `app/`, `config/`, `database/`, `storage/`, `vendor/`, `composer.json`, and `composer.lock` into `/home/USERNAME/`, beside `public_html`, not inside it.

Do not upload `.git/`, `node_modules/`, a development `.env`, `storage/logs/*`, `storage/cache/*`, `storage/sessions/*`, or `storage/backups/*`. Keep each `.gitkeep` so the empty directories exist. `resources/` and `tailwind.config.js` are not used at runtime.

`public_html/router.php` is only for `php -S` on a development machine. Apache ignores it.

### If the FTP user cannot leave `public_html`

Extract the whole repository **inside** the account `public_html`, so you get `public_html/app`, `public_html/.env`, and `public_html/public_html/index.php`. The `.htaccess` and `index.php` at the repository root then become the document root and refuse direct access to `app`, `config`, `storage`, `vendor`, `database`, and `.env`. Prefer the sibling layout above. Use this one only when the account cannot store files next to `public_html`.

## 1. Upload

Use whichever tool you already have. The tree above is the same either way.

- **File Manager:** open the home directory (the folder that contains `public_html`, not the inside of `public_html`). Upload a zip of `app`, `config`, `database`, `storage`, `vendor`, `composer.json`, and `composer.lock`, then Extract. Upload a second zip of the repository `public_html` contents into the account `public_html` and Extract there. Leave the existing `public_html/images/` folder in place until the seeder has run.
- **FTP:** connect to the account home. The main cPanel FTP user is not jailed to `public_html`. An extra FTP user created inside `public_html` cannot see `app/`.
- **Git Version Control:** clone into a folder under the home directory, then copy or deploy so the live tree matches the diagram. Cloning straight into `public_html` is the fallback layout, not the preferred one.

## 2. MySQL

1. cPanel → **MySQL Databases**. Create a database and a user. cPanel prefixes both names, for example `USERNAME_portfolio` and `USERNAME_portfolio`.
2. Add the user to the database with **ALL PRIVILEGES**.
3. **phpMyAdmin** → that database → Import → `database/schema.sql`.

`DB_HOST` on this account is `localhost`.

## 3. PHP

cPanel → **MultiPHP Manager** → `muyiwa-dev.com.ng` → PHP **8.1** or newer.

cPanel → **Select PHP Version** (or MultiPHP INI) and enable:

- `pdo_mysql`
- `gd` (WebP support)
- `mbstring`
- `fileinfo`
- `openssl`
- `curl`
- `zip`

`exif` is optional. It is used only to honour JPEG orientation before the file is re-encoded.

`public_html/.user.ini` sets upload size, memory, and `display_errors = Off`. PHP-FPM picks it up within a few minutes. Do not put `php_value` or `php_flag` in `.htaccess`; on this host those directives return 500.

## 4. `.env`

Create `/home/USERNAME/.env` (home directory, not inside `public_html`). Start from `.env.example`. It must not be committed.

```
APP_ENV=production
APP_URL=https://muyiwa-dev.com.ng

DB_HOST=localhost
DB_PORT=3306
DB_NAME=USERNAME_portfolio
DB_USER=USERNAME_portfolio
DB_PASS=the-password-from-cPanel

SMTP_HOST=
SMTP_PORT=587
SMTP_USER=
SMTP_PASS=
SMTP_ENCRYPTION=tls
SMTP_FROM_EMAIL=iamemmaafolayan@gmail.com
SMTP_FROM_NAME=Emmanuel Afolayan

LEGACY_ASSET_BASE=https://muyiwa-dev.com.ng
SEED_ADMIN_PASSWORD=
```

Leave `SMTP_*` empty to use PHP `mail()`. Filling `SMTP_HOST` makes PHPMailer use these values until a host is saved under Admin → Settings, which then takes over. Leave `SEED_ADMIN_PASSWORD` empty to install the password documented in `DEPLOYMENT.md`.

File mode `640` is enough. The web user on cPanel is the account user.

## 5. Permissions

cPanel PHP runs as the account user, so the owner can be that user and the mode can stay owner-writable:

| Path | Mode |
| --- | --- |
| `app/`, `config/`, `vendor/`, `database/` | `755` directories, `644` files |
| `.env` | `640` |
| `storage/`, `storage/logs/`, `storage/cache/`, `storage/sessions/`, `storage/backups/` | `755` |
| `public_html/uploads/`, `uploads/projects/`, `uploads/profile/`, `uploads/cv/` | `755` |

Do not use `777`. Every admin save still clears `storage/cache`.

## 6. Seed once

The old site's `/images/` files have to answer at `LEGACY_ASSET_BASE` while the seeder runs. Run it **before** you delete `public_html/images/`. Replacing `index.php` and `assets/` first is fine. Deleting `images/` first makes the downloads fail; the seeder then writes labelled placeholders and continues, and the site still does not hotlink the old host.

cPanel → **Terminal**, from `/home/USERNAME`:

```bash
php database/seed_from_legacy.php
```

If that `php` is older than 8.1:

```bash
/opt/cpanel/ea-php81/root/usr/bin/php database/seed_from_legacy.php
```

Use `ea-php82` or `ea-php83` when that is the version selected for the domain.

If Terminal is disabled, add a one-time cron job with that command and the home directory as the working path, confirm the site has content, then delete the cron. Running the seeder again without `--force` does nothing. `--force` replaces the seeded content.

## 7. First login

Open `/admin/login`. The email and the initial password are in `DEPLOYMENT.md`. Change the password on the screen the site shows next. It will not leave that screen until the password changes.

## 8. SSL, then HTTPS

Do this in order. Forcing HTTPS before a certificate exists makes the domain fail to load.

1. Upload and confirm `http://muyiwa-dev.com.ng/` loads.
2. cPanel → **SSL/TLS Status** → Run AutoSSL for `muyiwa-dev.com.ng`. Wait until the certificate is valid. Leave **Force HTTPS Redirect** off during this step.
3. Visit `https://muyiwa-dev.com.ng/` and confirm the padlock.
4. In `public_html/.htaccess`, uncomment the five `RewriteCond` / `RewriteRule` lines in the force-HTTPS block. Or turn on cPanel **Force HTTPS Redirect**. One of those is enough.
5. `www` already redirects to the apex host. Before the certificate exists, that redirect stays on HTTP.

## 9. Smoke checks

- `https://muyiwa-dev.com.ng/` shows the portfolio and local `/uploads/` images, not links to the old image paths.
- `/projects` and one project page.
- `/contact` submits and the message appears under Admin → Messages.
- `/admin` opens only after login.
- Upload the CV PDF under Admin → CV, then `/cv` downloads it.
- `/sitemap.xml` and `/robots.txt` use `https://muyiwa-dev.com.ng`.
- `https://muyiwa-dev.com.ng/app/` and `https://muyiwa-dev.com.ng/.env` are not readable.

Errors are written to `storage/logs/app.log` and `storage/logs/php.log`. The browser does not show PHP errors.
