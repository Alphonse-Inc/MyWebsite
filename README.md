# Emmanuel Afolayan — portfolio

Personal portfolio for Emmanuel Oluwamuyiwa Afolayan. It is a PHP 8.1+ MVC application for Apache/cPanel shared hosting, with MySQL or MariaDB and no production build step.

Public pages read their copy from the database. Defaults live in `config/site.php` and are inserted by `php database/seed_from_legacy.php`.

## Layout

- `app/` — controllers, models, views, core, services (not web-accessible)
- `config/` — application settings and default copy
- `database/schema.sql` — InnoDB schema
- `database/seed_from_legacy.php` — one-time content and image import
- `public_html/` — document root (`index.php`, `.htaccess`, assets, uploads)
- `storage/` — logs, cache, sessions, backups

Production is Apache. The PHP built-in server is only a local check:

```bash
php -S 127.0.0.1:8080 -t public_html public_html/router.php
```

See `HOSTING.md` for the cPanel upload to https://muyiwa-dev.com.ng. See `DEPLOYMENT.md` for the first login. See `MISSING_DATA.md` for facts that still need confirmation.
