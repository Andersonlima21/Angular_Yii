# Yii 2 — API REST (Backend)

API REST de estudo construída com Yii 2, servindo dados para o SPA AngularJS em `../yii-frontend-angular`.

## Requisitos

- PHP 7.4+
- Extensão `pdo_sqlite` habilitada
- Composer

## Instalação e execução

```bash
composer install
php yii migrate          # cria/atualiza o banco SQLite em runtime/database.sqlite
php yii serve            # http://localhost:8080
```

Alternativa com Docker:

```bash
docker-compose up -d     # http://127.0.0.1:8000
```

## Banco de dados

SQLite em `runtime/database.sqlite` (configurado em `config/db.php`). O README original do template referenciava MySQL — ignore, o projeto usa exclusivamente SQLite. `PDO::ATTR_STRINGIFY_FETCHES` está desligado para que inteiros e floats não virem strings.

## Endpoints REST

Todos os recursos respondem no envelope `{ success, type, data | message }`.

| Recurso               | Base URL             |
|-----------------------|----------------------|
| Usuários              | `/user-api`          |
| Configurações         | `/user-config`       |
| Perfis                | `/user-profile`      |
| Settings de perfil    | `/user-profile-setting` |

Rotas padrão geradas por `yii\rest\UrlRule` (GET, POST, PUT, PATCH, DELETE). Actions extras (ex.: `toggle-active`) são declaradas antes do `UrlRule` em `config/web.php`.

## Testes

```bash
tests/bin/yii migrate           # aplica migrations no banco de teste

vendor/bin/codecept run         # unit + functional
vendor/bin/codecept run unit
vendor/bin/codecept run unit tests/unit/models/UserTest.php
vendor/bin/codecept run unit tests/unit/models/UserTest.php:testMetodo

# com cobertura
vendor/bin/codecept run --coverage --coverage-html --coverage-xml
# saída em tests/_output/
```

O banco de teste é um SQLite separado em `runtime/test_database.sqlite` (ver `config/test_db.php`).

**Acceptance tests** requerem configuração adicional: renomear `tests/acceptance.suite.yml.example`, instalar Selenium e subir `tests/bin/yii serve`.

## Arquitetura

Veja `CLAUDE.md` na raiz do projeto e `yii2-app-basic/CLAUDE.md` para documentação detalhada da arquitetura (fluxo de requisição, service layer, validação, timestamps).
