# Secure Storage - Laravel Demo

## Overview
Small Laravel app to manage secure file storage with:
- Roles: admin / user
- Groups with quotas
- Per-user, per-group, global quota precedence
- Forbidden extensions management
- ZIP inspection to reject zips containing forbidden files
- Vanilla JS uploader using fetch (no page reload)

Spec source: provided test PDF. :contentReference[oaicite:1]{index=1}

## Requirements
- PHP 8.1+
- Composer
- PostgreSQL
- Laravel 9+ (or 10)
- Write access to `storage/` (run `php artisan storage:link`)

## Quick install
1. Clone repo
2. Copy `.env.example` to `.env` and set DB credentials
3. `composer install`
4. `php artisan key:generate`
5. Create DB in Postgres (`secure_storage`)
6. `php artisan migrate`
7. `php artisan db:seed`
8. `php artisan storage:link`
9. `php artisan serve`

Default example accounts (created by seeder):
- Admin: `admin@example.com` / `adminpass`
- User: `user@example.com` / `userpass`

## How it works (flow)
- A user logs in. The user has an effective quota: check per-user quota -> group quota -> global quota (stored in `settings.global_quota_bytes`).
- When user uploads:
  1. Server receives file.
  2. Server checks extension against `forbidden_extensions`.
  3. If `.zip`, server opens it (ZipArchive) and iterates internal entries; if any internal extension is forbidden the whole zip is rejected with a descriptive error.
  4. Server computes `(currentUsedBytes + newFileSize) > effectiveQuota` and rejects if quota exceeded.
  5. On success file saved in `storage/app/public/uploads`, and DB record created.
- UI uses `public/js/uploader.js` to make the upload via `fetch` and display success/error messages without page reload.


## DB schema
- `users` (role, quota_bytes, group_id)
- `groups` (quota_bytes)
- `stored_files` (user_id, original_name, path, size_bytes)
- `forbidden_extensions` (ext)
- `settings` (key,value) for global quota

