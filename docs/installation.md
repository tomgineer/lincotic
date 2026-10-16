# Installation

This guide covers a fresh Lincotic installation for local development and production. Lincotic is a CodeIgniter application backed by MySQL or MariaDB. The repository includes compiled frontend assets, so Node.js is optional unless you intend to change the CSS or JavaScript.

## Requirements

Install the following software before continuing:

- PHP 8.5 or later in the PHP 8 series (the current Composer constraint is `^8.5`)
- PHP extensions: `ctype`, `dom`, `filter`, `intl`, `mbstring`, `mysqli`, and `openssl`
- Composer 2
- MySQL or MariaDB with InnoDB and `utf8mb4` support
- A web server such as Apache 2.4 or Nginx for production
- Git, if installing directly from the repository
- Node.js and npm only when rebuilding frontend assets

The PHP CLI and the PHP runtime used by the web server should use the same PHP version and extensions. Lincotic also requires `zlib.output_compression` to be disabled.

After installing the PHP dependencies, the project can verify its Composer platform requirements:

```shell
composer check-platform-reqs --no-dev
php spark phpini:check
```

## 1. Download Lincotic

Clone the repository and enter the project directory:

```shell
git clone https://github.com/tomgineer/lincotic.git
cd lincotic
```

For a production installation, check out the release or commit that you want to deploy instead of relying on a moving branch.

## 2. Install PHP dependencies

For local development and testing:

```shell
composer install
```

For production:

```shell
composer install --no-dev --optimize-autoloader
```

The `vendor/` directory is not stored in Git and must exist on every installed instance.

## 3. Create the database

Create an empty database and a dedicated database account. The following example must be run from an administrative MySQL or MariaDB session; replace the username and password before using it:

```sql
CREATE DATABASE lincotic
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER 'lincotic'@'localhost'
    IDENTIFIED BY 'replace-with-a-long-random-password';

GRANT ALL PRIVILEGES ON lincotic.*
    TO 'lincotic'@'localhost';

FLUSH PRIVILEGES;
```

Import the supplied schema into the empty database:

```shell
mysql --host=localhost --user=lincotic --password lincotic < database/schema.sql
```

The command above is for a POSIX-compatible shell and prompts for the database password. On Windows, run the equivalent command through Command Prompt, or import [`database/schema.sql`](../database/schema.sql) through phpMyAdmin or another database administration tool.

The schema file is the source for a fresh installation. Do not run `php spark migrate` in place of importing it: the repository currently contains no application migration files.

## 4. Configure the environment

Create the local environment file from the supplied template.

On Linux or macOS:

```shell
cp env .env
```

On Windows PowerShell:

```powershell
Copy-Item env .env
```

Edit `.env` and configure at least the application URL, database connection, and root username:

```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://links.example.com/'
app.defaultLocale = en
app.forceGlobalSecureRequests = true

lincotic.rootUsername = civic
lincotic.cache.enabled = true
lincotic.cache.duration = 3600

database.default.hostname = localhost
database.default.port = 3306
database.default.database = lincotic
database.default.username = lincotic
database.default.password = 'replace-with-the-database-password'
database.default.DBDriver = MySQLi

cookie.secure = true
```

Important configuration details:

- `app.baseURL` must be the complete public URL and must end with `/`.
- Use `CI_ENVIRONMENT = development`, an HTTP URL, `app.forceGlobalSecureRequests = false`, and `cookie.secure = false` only for local development.
- Set `lincotic.rootUsername` to the account whose hub should appear at `/`.
- Usernames consist of lowercase letters and numbers. The configured root username must match the account exactly.
- Keep `.env` secret. It is ignored by Git and must never be placed inside the public web directory.
- On a production site served directly over HTTPS, enable both secure requests and secure cookies as shown above.
- If TLS terminates at a reverse proxy, configure the trusted proxy IPs in `Config\App::$proxyIPs` and ensure the proxy sends the correct forwarded protocol before enabling forced HTTPS. An incorrect proxy configuration can cause redirect loops.

Generate a unique application encryption key after creating `.env`:

```shell
php spark key:generate
```

Do not copy an encryption key from another installation or commit the generated key.

## 5. Create the root administrator

Create an active administrator whose username matches `lincotic.rootUsername`:

```shell
php spark shield:user create -n civic -e you@example.com -g admin
```

Replace `civic` and the email address with your own values. The command prompts for the password. Lincotic disables public registration, so additional accounts are created by an administrator from the application or with the same Shield command.

You can inspect the installed accounts and groups with:

```shell
php spark shield:user list
```

If `/` returns a not-found page after installation, verify that:

1. `lincotic.rootUsername` matches the created username.
2. The account is active.
3. The account was assigned to the `admin` group.

## 6. Frontend assets

Compiled CSS and JavaScript are committed in `public/css/app-dist.css`, `public/js/app-dist.js`, and `public/js/tiptap-dist.js`. A normal installation therefore does not require Node.js.

If you modify files under `theme/`, install the exact frontend dependencies and rebuild the assets:

```shell
npm ci
npm run build
```

For frontend development with file watchers:

```shell
npm run dev
```

## 7. Writable directories

The web-server user needs read access to the application and write access to `writable/`, especially its cache, log, session, and upload directories.

A typical Linux setup, where `www-data` is the web-server group, is:

```shell
sudo chgrp -R www-data writable
sudo chmod -R g+rwX writable
chmod 640 .env
```

Adjust the group for your server. Do not solve permission problems by applying `777` to the project. The repository root, `app/`, `vendor/`, and `.env` should not be writable by the web process.

## 8. Run locally

For local development, start CodeIgniter's development server from the repository root:

```shell
php spark serve
```

Open the URL printed by the command, normally `http://localhost:8080/`. Set `app.baseURL` in `.env` to the same URL, including its trailing slash.

The development server is not intended for public production traffic.

## 9. Configure a production web server

The public document root must point to the repository's `public/` directory, not the repository root. This keeps `.env`, application code, logs, sessions, and Composer packages outside the web-accessible directory.

For example, if Lincotic is installed at `/var/www/lincotic`, use:

```text
/var/www/lincotic/public
```

The public URL should not contain `/public`.

### Apache

Enable `mod_rewrite`, point the virtual host to `public/`, and allow the included `.htaccess` rules:

```apache
<VirtualHost *:80>
    ServerName links.example.com
    DocumentRoot /var/www/lincotic/public

    <Directory /var/www/lincotic/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/lincotic-error.log
    CustomLog ${APACHE_LOG_DIR}/lincotic-access.log combined
</VirtualHost>
```

Enable the site, reload Apache, and configure HTTPS with your normal certificate tooling. The repository's `public/.htaccess` contains the generic front-controller rules. `public/example.htaccess` contains deployment-specific examples and must not be copied unchanged because it includes a hard-coded hostname.

### Nginx with PHP-FPM

The following is a minimal HTTP example. Adjust the PHP-FPM socket and add your normal TLS configuration:

```nginx
server {
    listen 80;
    server_name links.example.com;
    root /var/www/lincotic/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ ^/index\.php(?:/|$) {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ \.php$ {
        return 404;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Reload Nginx and PHP-FPM after validating their configuration.

### Shared hosting

Set the domain or subdomain document root to Lincotic's `public/` directory. Keep the rest of the project one level above or elsewhere outside the public directory. If the hosting provider cannot change the document root, do not expose the entire repository as a workaround; use a subdomain with its own document root or a hosting layout that supports CodeIgniter's public-directory model.

## 10. Verify the installation

Run these checks from the project root:

```shell
composer check-platform-reqs --no-dev
php spark phpini:check
php spark routes
php spark shield:user list
```

Then verify the application in a browser:

1. `/` loads the configured root user's public hub.
2. `/login` accepts the administrator credentials.
3. The administrator can create a collection and a link.
4. `/@username` loads the correct user's public hub in a signed-out browser.
5. Hidden content is not visible when signed out.
6. CSS, JavaScript, images, and icons load without `/public` appearing in their URLs.

Lincotic performs its lightweight cache, expired-session, and `robots.txt` maintenance after a successful login, so a separate cron job is not required for a standard installation.

## Troubleshooting

### Every application route returns 404

- Confirm that the document root points to `public/`.
- On Apache, enable `mod_rewrite` and permit `.htaccess` overrides.
- On Nginx, confirm that `try_files` forwards requests to `index.php`.
- Run `php spark routes` to confirm that the application routes are available.

### The root page returns 404

- Confirm that `lincotic.rootUsername` is present in `.env`.
- Confirm that the matching user exists and is active with `php spark shield:user list`.
- Usernames are case-sensitive in configuration and should contain only lowercase letters and numbers.

### Database connection errors

- Confirm the hostname, port, database name, username, and password in `.env`.
- Confirm that the PHP `mysqli` extension is enabled for both CLI and PHP-FPM or Apache.
- Confirm that the database account is allowed to connect from the application's host.
- Confirm that `database/schema.sql` was imported into the configured database.

### Login or CSRF failures

- Ensure `writable/session/` is writable by the web-server user.
- Ensure `app.baseURL` exactly matches the scheme and hostname used in the browser.
- Use `cookie.secure = true` only when the browser reaches the site over HTTPS.
- Clear old browser cookies after changing the domain, scheme, or cookie configuration.

### Redirect loop after enabling HTTPS

If TLS is terminated by a proxy or load balancer, configure the proxy as trusted and forward the original HTTPS scheme to PHP. Until that is correct, leave `app.forceGlobalSecureRequests` disabled and enforce HTTPS at the proxy.

### Blank page or HTTP 500

- Check the newest file in `writable/logs/`.
- Verify write permissions under `writable/`.
- Run `composer check-platform-reqs --no-dev`.
- Run `php spark phpini:check` and disable `zlib.output_compression`.
- In production, keep `display_errors` disabled and use the application and web-server logs.

### Styles or scripts do not load

- Confirm that `public/css/app-dist.css`, `public/js/app-dist.js`, and `public/js/tiptap-dist.js` exist.
- Confirm that the web server points to `public/` and that `app.baseURL` is correct.
- If you changed frontend source files, run `npm ci` followed by `npm run build`.

## Updating an installation

Before updating, back up the database, `.env`, and any installation-specific files. Then update the application code and PHP dependencies:

```shell
git pull --ff-only
composer install --no-dev --optimize-autoloader
composer check-platform-reqs --no-dev
```

Compiled frontend assets are versioned in the repository. Run `npm ci && npm run build` only if you maintain local frontend changes or the release notes explicitly require a rebuild.

Review release notes and database changes before updating. Do not re-import the complete fresh-installation schema over an existing database.
