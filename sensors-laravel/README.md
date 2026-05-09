# sensors-laravel — Setup (Laravel + Vite)

This is a simple step-by-step guide to set up the Laravel app + frontend.

## Prerequisites

- PHP **8.3+**
- Composer
- Node.js **(LTS recommended)** and **npm**
- A database (optional depending on your environment)
  - This project runs migrations for Laravel default tables (users/sessions/etc.)

## 1) Clone / open the project

```bash
cd c:/laragon/www/sensors-project/sensors-laravel
```

## 2) Copy environment file

```bash
copy .env.example .env
```

Edit `.env` as needed:
- `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

## 3) Install PHP dependencies

```bash
composer install
```

## 4) Install frontend dependencies (Vite)

```bash
npm install
```

## 5) Generate app key

```bash
php artisan key:generate
```

## 6) Run migrations

```bash
php artisan migrate --force
```

## 7) (Optional) Seed database

This project has a default `DatabaseSeeder` that creates a sample user.

```bash
php artisan db:seed
```

## 8) Run the app (dev)

### Terminal A: Laravel server

```bash
php artisan serve
```

### Terminal B: Vite dev server

```bash
npm run dev
```

### Terminal C: (If you use queues) start queue worker

This project includes a queue listener in the `composer.json` dev script.

```bash
php artisan queue:listen --tries=1 --timeout=0
```

## 9) Open the app

- Dashboard page: **http://localhost:8000/dashboard**
- API endpoints:
  - **GET /api/sensors** (latest values)
  - **GET /api/history** (last 20 records)
- CSV download:
  - **GET /download**

## Useful scripts

From `composer.json`:

- One-shot setup script (installs deps + builds frontend):
  - `composer run setup`
- Dev script (runs server + queue + Vite concurrently):
  - `composer run dev`

## Notes about data

The dashboard reads sensor values from:
- `storage/app/sensors.csv`

If it doesn’t exist, the controller creates it with a header line.

