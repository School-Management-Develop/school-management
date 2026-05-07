# Deploy this project to Laravel Cloud

This project was prepared for Laravel Cloud with a bundled SQL import command.

## 1) Attach a MySQL database

On Laravel Cloud, add a Laravel MySQL database to the environment and re-deploy after attaching it.

## 2) Environment variables

Set these in your environment if they are not already set:

```env
APP_NAME="School Management"
APP_ENV=production
APP_DEBUG=false
FILESYSTEM_DISK=s3
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

`APP_KEY` should be generated once in the deployed environment.

## 3) Build and deploy commands

Recommended build command:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Recommended deploy command:

```bash
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link || true
```

## 4) Import the SQL data

After the app is deployed and the database is attached, open the Laravel Cloud terminal for the environment and run:

```bash
php artisan school:import-sql --fresh
```

This imports the bundled file at `database/sql/school_management.sql`.

## 5) Redeploy after database attachment

When you attach a database to an environment, re-deploy so the database environment variables are available to the app.

## Notes

- The original `.env` file was removed from this package for safety.
- Uploaded item images use Laravel's `public` disk. `php artisan storage:link` is included in the deploy command.
