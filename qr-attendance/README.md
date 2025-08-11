# QR-based Attendance Tracking for Tutoring Service

This is a Laravel-based web application for QR attendance tracking with three roles: Super Admin, Admin, and Students.

- Super Admin: Full system access; manages Admins and global settings
- Admin: Manages tutoring sessions, displays QR codes, reviews/export attendance
- Student: Scans QR to check in, views their attendance history

## Stack
- Laravel 11.x
- Breeze (Blade) for auth scaffolding
- Spatie Laravel Permission for roles/permissions
- Simple QrCode for QR generation
- SQLite (default) or MySQL/PostgreSQL

## Quickstart

1) Create a new Laravel app

```bash
composer create-project laravel/laravel qr-attendance
cd qr-attendance
```

2) Install Breeze (Blade)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

3) Install dependencies

```bash
composer require spatie/laravel-permission
composer require simplesoftwareio/simple-qrcode
```

4) Publish Spatie config & migrations

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"
```

5) Configure database

- For SQLite (quickest):
  - Create the DB file: `touch database/database.sqlite`
  - In `.env` set:
    ```env
    DB_CONNECTION=sqlite
    DB_DATABASE=./database/database.sqlite
    ```
- Or configure MySQL/PostgreSQL in `.env`.

6) Copy the app files from this folder into your Laravel app

Copy everything under `app`, `database`, `resources`, and `routes` from this repository into the same locations in your Laravel app (allow overwrites of newly created files only; do not delete existing vendor files).

7) Update `app/Models/User.php`

Add Spatie's trait:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    // ...
}
```

8) Migrate and seed

```bash
php artisan migrate
php artisan db:seed --class=RoleSeeder
```

9) Create users and assign roles

- Register a user via the app, then in `tinker`:
  ```bash
  php artisan tinker
  >>> $u = App\Models\User::where('email','you@example.com')->first();
  >>> $u->assignRole('super-admin');
  ```
- Super Admin can create Admins and assign role `admin`.

10) Web installer

- Point your browser to your app URL. You will be redirected to `/install`.
- Complete steps: requirements -> environment -> create super admin -> finish.
- After finishing, the app will be marked installed and the installer is disabled.

11) Serve the app

```bash
php artisan serve
```

Visit: `http://127.0.0.1:8000`

## Core Concepts

- Sessions: A scheduled tutoring session (title, tutor, time, location)
- QR Token: Time-bound token stored hashed on the session; Admin displays a QR that encodes the scan URL with the token
- Scan Flow: Student scans QR -> logs in if needed -> token validated -> attendance recorded

## Roles
- `super-admin`: everything
- `admin`: manage sessions, show QR, view/export attendance
- `student`: scan and view own history

## Routes Overview
- `/dashboard` role-aware landing
- `/sessions` CRUD (admin)
- `/sessions/{session}/qr` display QR (admin)
- `/qr/scan` token-authenticated scan endpoint (students)

## Security Notes
- Tokens are random, stored only as SHA-256 hash salted with `APP_KEY`
- Tokens auto-expire (configurable expiry window)
- Scan route requires auth; unauthenticated users are redirected to login then back (intended)

## Exports
- Attendance can be exported as CSV from the admin interface

## Customization
- Adjust token TTL in `QRController` (`TOKEN_TTL_MINUTES`)
- Extend `TutoringSession` fields as needed (subjects, groups, etc.)

## Production deployment

- Web server
  - Nginx: point root to `public/`, enable `try_files $uri /index.php?$query_string;`
  - Apache: enable mod_rewrite and use Laravel `.htaccess` in `public/`
- PHP: 8.2+ with required extensions (bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml)
- Permissions: `storage` and `bootstrap/cache` writable by the web user
- Environment: set `APP_ENV=production`, `APP_DEBUG=false`, proper `APP_URL`
- Caching (recommended):
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
  ```
- Queues (optional): configure `QUEUE_CONNECTION` and run a worker if needed
- Backups: schedule regular DB and storage backups
- HTTPS: enforce TLS and set `SESSION_SECURE_COOKIE=true` if behind HTTPS
- Timezone: set `APP_TIMEZONE` if different from default