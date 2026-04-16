# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project context

Yii 2 Basic Project Template used as a PHP study project. Comments throughout the code (especially `config/web.php` and `services/UserService.php`) are written in Portuguese and often contrast Yii idioms with Laravel/Eloquent equivalents — preserve that tone and language when extending those files.

The database is **SQLite** at `runtime/database.sqlite` (see `config/db.php`), not MySQL as the README implies. `PDO::ATTR_STRINGIFY_FETCHES` is disabled so numeric columns are returned as int/float.

## Common commands

```bash
# Serve the web app (dev)
php yii serve                         # http://localhost:8080
# or via Docker
docker-compose up -d                  # http://127.0.0.1:8000

# Console / migrations
php yii migrate                       # apply migrations in migrations/
php yii migrate/create <name>
tests/bin/yii migrate                 # test DB (config/test_db.php)

# Tests (Codeception)
vendor/bin/codecept run               # unit + functional
vendor/bin/codecept run unit
vendor/bin/codecept run unit tests/unit/models/UserTest.php
vendor/bin/codecept run unit tests/unit/models/UserTest.php:testMethodName
vendor/bin/codecept run --coverage --coverage-html --coverage-xml
# acceptance requires renaming tests/acceptance.suite.yml.example and a running Selenium + `tests/bin/yii serve`

# Deps
composer install
```

## Architecture

Two parallel controller styles coexist intentionally — don't unify them:

- **`SiteController`** + `models/LoginForm.php`, `ContactForm.php`, `User.php` — stock Yii basic template (login, contact, hardcoded in-memory users). Left untouched as reference.
- **`UserApiController`** + `services/UserService.php` + `models/UserApi.php` — the study surface. A JSON REST layer on the `users` SQLite table.

### Request flow for the REST layer

`config/web.php` has an extended comment block documenting this exactly; read it before touching routing. Summary:

1. `urlManager` with `yii\rest\UrlRule` (controller `user-api`, `pluralize => false`) expands to the 5 standard REST routes (`GET/POST /user-api`, `GET/PUT/PATCH/DELETE /user-api/<id:\d+>`).
2. Yii converts the route id (`user-api`) → `UserApiController` via kebab→Camel + `Controller` suffix under namespace `app\controllers`.
3. `UserApiController::__construct` receives `UserService` via Yii's DI container — services are plain classes, not registered anywhere explicitly; DI resolves by constructor type hint.
4. Responses go through `contentNegotiator` → JSON. Every action returns the envelope `{ success, type, data|message }` and sets `Yii::$app->response->statusCode` manually on errors (400) and create (201) / delete (204).
5. To add non-REST actions (e.g. `actionArchive`), add a short-form rule **before** the `UrlRule` in `config/web.php`.

### Service layer convention

`UserService` has each method implemented twice:

- The primary version (`findAll`, `findById`, `create`, `update`) uses the **Query Builder / `createCommand`** — explicit SQL-ish style, wrapped in `try/catch` that rethrows as `ServerErrorHttpException`, and uses transactions for writes.
- A `_new` sibling (`findAll_new`, `create_new`, etc.) shows the **ActiveRecord** equivalent (`UserApi::find()`, `$model->save()`).

Both styles are kept side-by-side on purpose (study comparison). When adding features, follow the Query Builder pattern used by the non-suffixed methods unless the task is explicitly about ActiveRecord.

### Validation

`UserApiController::actionCreate`/`actionUpdate` instantiate `UserApi` purely to run `rules()` (required, email, unique) against the request body before delegating to the service — the model is **not** saved there. Validation errors are flattened into a single `\Exception` message joined by ` | `. Keep this pattern if you add new write endpoints.

### Timestamps

`UserApi` uses `TimestampBehavior` with `new Expression("datetime('now')")` — SQLite-specific. `UserService::create` replicates the same expression manually; `update` uses PHP `date('Y-m-d H:i:s')`. If you change the column storage format, update both places.

## Config layout

- `config/web.php` — HTTP app (includes the long routing comment block).
- `config/console.php` — CLI app, `controllerNamespace => app\commands`.
- `config/db.php` — shared SQLite connection.
- `config/test.php` / `config/test_db.php` — Codeception.
- `config/params.php` — user params (e.g. `adminEmail`).
- `config/__autocomplete.php` — IDE helper only, not loaded at runtime.
