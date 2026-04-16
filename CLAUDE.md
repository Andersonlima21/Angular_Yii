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

If the backend port changes, update `yii-frontend-angular/app/services/apiConfig.js`:
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

**Routing** uses UI-Router nested states, not `ngRoute`. The user edit page has nested tab states:
- `users.edit.info`, `users.edit.configs`, `users.edit.profiles`, `users.edit.settings`

Each tab is a separate `<ui-view>` loading its own controller + template.

**Services** (`app/services/`) each wrap a REST resource and unwrap the envelope — callers receive plain `data` on success, or a rejected promise with `message` on error. Never return the raw envelope to controllers.

**Custom components** to be aware of:
- `app/filters/` — date formatting filter
- `app/directives/` — phone input mask directive

### Data flow for nested resources

When editing a user, `GET /user-api/<id>` returns the user record with embedded `configs` and `profiles` arrays. The frontend tabs read from this cached response rather than making separate API calls per tab.
