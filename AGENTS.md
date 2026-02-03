# Repository Guidelines

## Project Structure & Module Organization
- `app/` contains Laravel application code (controllers in `app/Http`, models in `app/Models`, services in `app/Services`, repositories in `app/Repositories`).
- `routes/` holds route definitions (`web.php`, `api.php`).
- `resources/` contains Blade views, static assets, and frontend sources.
- `public/` is the web root for built assets.
- `database/` includes migrations, seeders, and factories.
- `tests/` contains automated tests (Unit, Feature, plus Integration and Performance helpers).
- `config/`, `bootstrap/`, and `storage/` follow standard Laravel conventions.

## Build, Test, and Development Commands
- `composer install` installs PHP dependencies.
- `npm install` installs frontend tooling.
- `composer run dev` starts the full local stack (Laravel server, queue worker, log viewer, Vite).
- `npm run dev` runs the Vite dev server only.
- `npm run build` builds production frontend assets.
- `php artisan migrate` runs database migrations.
- `php artisan test` runs the PHPUnit test suite.
- `php artisan test --coverage` runs tests with coverage output.

## Coding Style & Naming Conventions
- Indentation: 4 spaces (see `.editorconfig`); YAML uses 2 spaces.
- PHP style: follow Laravel conventions; prefer `StudlyCase` class names and `camelCase` methods.
- Formatting: use Laravel Pint when touching PHP (`vendor/bin/pint`).
- Tests: class names end in `Test` and live under `tests/` by scope (e.g., `tests/Unit/Services`).

## Testing Guidelines
- Primary runner: PHPUnit via `php artisan test`.
- Suites configured in `phpunit.xml` include Unit and Feature; run other tests by path (e.g., `php artisan test tests/Integration`).
- Keep new tests close to the layer they validate and mirror naming patterns (e.g., `UrlShorteningServiceTest`).

## Commit & Pull Request Guidelines
- Commit history uses short, direct summaries (e.g., “Hardening automated tests”); keep messages concise and imperative.
- PRs should include: a clear description, linked issues (if any), and screenshots for UI changes.
- Ensure `php artisan test` and `npm run build` pass before requesting review.

## Configuration & Security Notes
- Copy `.env.example` to `.env` and set `DB_*` and reCAPTCHA keys before running locally.
- Use Redis for caching in development when possible (`CACHE_STORE=redis`).
- Avoid committing secrets; prefer `.env` and Laravel config overrides.
