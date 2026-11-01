# Deployment

This site runs on Apache with PHP 8.1+ and MySQL or MariaDB. There is no Node process, Docker service, or server-side build in production. Tailwind was compiled once; the minified file is `public_html/assets/css/app.css`.

The document root must be `public_html`. The folders `app`, `config`, `storage`, `vendor`, and `database` stay outside the web root. On cPanel, upload those folders into the account home (next to `public_html`) and put the contents of this project's `public_html` directory into the account's `public_html`.

`vendor/` is committed. If you install dependencies yourself instead, run `composer install --no-dev --optimize-autoloader` in the project root and upload `vendor/` as well.

## PHP extensions

Required: PDO MySQL, GD with WebP, mbstring, fileinfo, openssl, curl, zip. `exif` is optional and is used only to correct JPEG orientation before the file is re-encoded.

Suggested limits in MultiPHP INI or `.user.ini`:

- `upload_max_filesize = 8M`
- `post_max_size = 16M`
- `memory_limit = 256M`
- `max_file_uploads = 20`

`display_errors` stays off. Errors are written to `storage/logs/`.

## Database

1. In cPanel, create a MySQL database and a user with all privileges on that database.
2. Open phpMyAdmin and import `database/schema.sql`.
3. Copy `.env.example` to `.env` in the project root (the home directory, not inside `public_html`).
4. Set `APP_URL` to the canonical https origin with no trailing slash, and set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS`. Do not commit `.env`.

## Seed

From the project root, after the schema import:

```bash
php database/seed_from_legacy.php
```

The seeder inserts the verified profile, resume, skills, and six projects, then downloads the legacy portrait, quote photo, hero video, and project covers into `public_html/uploads`. Files are re-encoded locally. The site does not hotlink the old host after a successful seed. If a download fails, the seeder writes a labelled placeholder and continues.

Run it again with `--force` only when you intend to replace that seeded content. Re-running without `--force` leaves existing content in place.

## First login

Admin URL: `/admin/login`

- Email: `iamemmaafolayan@gmail.com`
- Initial password: `Ea1f0f4f35#R7LEPr5F!`

Change this password immediately. The account is created with that requirement turned on, and the admin stays on the password screen until it is changed. Do not leave this password in a public ticket or pull-request description.

Forgot-password email needs the SMTP settings under Admin → Settings. Until SMTP is filled in, PHPMailer uses the server `mail()` transport, which many shared hosts restrict.

## SMTP

In Admin → Settings set the host, port, user, password, encryption (`tls`, `ssl`, or `none`), from address, and the notify address. Contact-form messages are stored even when mail fails. Turn on the automatic reply only after a test message arrives.

## Uploads and cache

Project images are stored in `public_html/uploads/projects/{id}/` as WebP and JPEG at thumbnail, medium, and large sizes. `public_html/uploads/.htaccess` refuses to execute PHP. Every admin save clears `storage/cache`.

`storage/` must be writable by the web user (`logs`, `cache`, `sessions`, `backups`).

## HTTPS

`public_html/.htaccess` redirects HTTP to HTTPS and `www` to the apex host when the request is not already secure. Localhost is excluded. If the certificate is not active yet, comment out those two redirect blocks, issue the certificate, then restore them.

## Local smoke test

Production is Apache. The built-in server is only a development check and does not read `.htaccess`:

```bash
php -S 127.0.0.1:8080 -t public_html public_html/router.php
```
