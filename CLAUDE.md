# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project context

Full-stack study project: a **Yii 2 REST API** backend consumed by an **AngularJS 1.x** single-page frontend. Comments in the backend code are written in Portuguese and often contrast Yii idioms with Laravel/Eloquent equivalents — preserve that tone when extending those files.

## Starting the dev environment

Two terminals required:

```bash
# Terminal 1 — Backend (http://localhost:8080)
cd yii2-app-basic
php yii serve

# Terminal 2 — Frontend (http://localhost:5500)
cd yii-frontend-angular
python -m http.server 5500
```

**Port conflict warning**: If another process (e.g. XAMPP's Apache) already occupies port 8080, `php yii serve` will fail silently and requests will return 200 with an empty body. Use `php yii serve --port=8081` and update `yii-frontend-angular/app/services/apiConfig.js` accordingly:
```javascript
angular.module('yiiApp').constant('API_BASE_URL', 'http://localhost:8080');
```

## Backend commands (run inside `yii2-app-basic/`)

```bash
composer install

php yii migrate                        # apply migrations
php yii migrate/create <name>
tests/bin/yii migrate                  # test DB (separate SQLite)

vendor/bin/codecept run                # unit + functional tests
vendor/bin/codecept run unit
vendor/bin/codecept run unit tests/unit/models/UserTest.php
vendor/bin/codecept run unit tests/unit/models/UserTest.php:testMethodName
vendor/bin/codecept run --coverage --coverage-html --coverage-xml

docker-compose up -d                   # alternative server on http://127.0.0.1:8000
```

Acceptance tests require: renaming `tests/acceptance.suite.yml.example` → `tests/acceptance.suite.yml`, a running Selenium server, and `tests/bin/yii serve`.

## Frontend (no build step)

The frontend has **no package.json or node_modules** — all libraries (AngularJS 1.8.3, UI-Router 1.0.30, Bootstrap 5.3.3) are loaded from CDN in `index.html`. There is no compile/transpile step.

Frontend JS syntax check (same as CI):
```bash
node --check app/**/*.js
```

## Architecture

### Backend — see `yii2-app-basic/CLAUDE.md` for full details

The REST layer handles 4 resource types: `user-api`, `user-config`, `user-profile`, `user-profile-setting`. All are registered in `config/web.php` via `yii\rest\UrlRule`. Every endpoint wraps its response in:

```json
{ "success": true, "type": "success", "data": {...} }
{ "success": false, "type": "exception", "message": "..." }
```

CORS is handled per-controller (in `behaviors()`), not globally.

### Frontend — AngularJS 1.x with UI-Router

Module: `yiiApp` (defined in `app/app.js`).

**Routing** uses UI-Router nested states, not `ngRoute`. Actual state names (in `app/app.js`):
- `users` → user list
- `newUser` → create form
- `editUser` → parent edit state (resolve fetches user data before rendering)
- `editUser.info`, `editUser.configs`, `editUser.profiles` → tab child states

`editUser.settings` tab component exists under `app/components/tab-settings/` but is not yet wired into `app.js`.

**Services** (`app/services/`) each wrap a REST resource and unwrap the envelope — callers receive plain `data` on success, or a rejected promise with `message` on error. Never return the raw envelope to controllers.

**`userEditContext`** (`app/services/userEditContext.js`) is a shared-state service: the parent `userEdit` component publishes the loaded user object and a `reload()` function; child tab components read from it instead of making their own API calls.

**Post-create workaround**: `POST /user-api` returns only a success string, not the created resource. `userCreate` works around this by calling `findAll()` after creation and filtering by email (which is unique).

**Custom components** to be aware of:
- `app/filters/sqlDate.js` — formats SQLite `YYYY-MM-DD HH:MM:SS` timestamps as `dd/MM/yyyy HH:mm`
- `app/directives/phoneMask.js` — formats phone input as `(XX) XXXXX-XXXX`, validates 11 digits

### Data flow for nested resources

When editing a user, `GET /user-api/<id>` returns the user record with embedded `configs` and `profiles` arrays. The frontend tabs read from this cached response rather than making separate API calls per tab.
