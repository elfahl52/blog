# Laravel Blog Platform

A Laravel 12 blog with a public reading experience and a role-protected publishing dashboard. Readers can search and browse published articles, filter by category or tag, and comment. Authors manage their own articles; administrators manage all articles, categories, and tags.

## ✨ Features

- Article drafts, scheduled publishing, and public article pages
- Search, category and tag filters, comments, and session-based view counts
- Image uploads with browser-side resizing and WebP conversion when it reduces file size
- Registration, login, logout, and role-based dashboard access
- Responsive Blade UI using Bootstrap 5, with Vite-managed JavaScript

## 🗂️ Project Structure

```text
app/
	Http/
		Controllers/
			Dashboard/
		Middleware/
		Requests/
	Models/
	Policies/
	Providers/
	Role.php
bootstrap/
config/
database/
	factories/
	migrations/
	seeders/
public/
	build/
resources/
	css/
	js/
	views/
		auth/
		dashboard/
		layouts/
		posts/
routes/
storage/
tests/
	Feature/
	Unit/
artisan
composer.json
package.json
vite.config.js
```

## 🧰 Requirements

- PHP 8.2 or newer with PDO MySQL enabled
- Composer
- Node.js and npm
- MySQL server

## 🚀 Local Setup

Create a MySQL database, then install the project from PowerShell:

```powershell
mysql -u root -p -e "CREATE DATABASE blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan storage:link
php artisan serve
```

Open the URL printed by Artisan, usually `http://127.0.0.1:8000`.

Update `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` to match your MySQL setup before running migrations. The example uses `blog`, `root`, and an empty password. Keep `.env` private and do not commit it.

Uploaded images are stored on Laravel's public disk. `php artisan storage:link` makes them available under `/storage`.

For frontend development with Vite hot reload, run `npm run dev` in a separate terminal while the Laravel server is running.

## 🔑 Demo Account and Seed Data

To create an administrator and sample categories, tags, and articles for local development, run:

```powershell
php artisan db:seed
```

- Email: `admin@example.com`
- Password: `password`

These credentials are available only after seeding. They are for local development; do not use them in production. The seeder is not idempotent, so run it on a fresh local database to avoid duplicate demo data.

## 👥 Roles

- `user`: default role for new registrations; can read and comment but cannot access the dashboard
- `author`: can create and manage their own articles
- `admin`: can manage all articles, categories, and tags

New registrations receive the `user` role. Assign author or admin access through a trusted administrative process; never expose role selection on public registration.

## 🧭 Main Routes

| Route | Purpose | Access |
| --- | --- | --- |
| `/` | Published articles and search | Public |
| `/posts/{slug}` | Article, image, and comments | Public; published articles only |
| `/category/{slug}` | Articles in a category | Public |
| `/tag/{slug}` | Articles with a tag | Public |
| `/login`, `/register` | Authentication | Guests |
| `/dashboard/posts` | Article management | Authors and admins |
| `/dashboard/categories` | Category management | Admins |
| `/dashboard/tags` | Tag management | Admins |

An article is public when its publish date is in the past or present. A blank publish date keeps it as a draft; a future date schedules it.

## 🧪 Tests

Run the automated suite with either command:

```powershell
php artisan test
```

or:

```powershell
php vendor/bin/phpunit
```

The test configuration uses an isolated in-memory SQLite database and covers authentication, publishing, visibility, comments, permissions, taxonomy, and image workflows. It does not connect to or modify the MySQL database configured in `.env`.

## 🚢 Production Checklist

- Set `APP_ENV=production`, `APP_DEBUG=false`, and the correct `APP_URL`
- Configure production MySQL credentials and run migrations during deployment
- Replace the demo admin credentials and protect all secrets
- Build frontend assets with `npm run build`
- Create the public storage link and configure persistent storage backups
- Review upload limits, HTTPS, logging, and database backups before launch

## 💡 Possible Next Steps

- Comment moderation
- Author profile pages
- Markdown editor
- Admin analytics and user management
