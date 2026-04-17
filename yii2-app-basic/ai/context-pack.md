# Context Pack — Regras Congeladas

> Carregado em TODA sessão. Máx 2-3 páginas. Mudanças requerem aprovação via `governance.md`.

---

## Stack

| Camada | Tecnologia | Versão |
|---|---|---|
| Framework | Yii 2 Basic | 2.x |
| Linguagem | PHP | 8+ |
| Banco (dev) | SQLite | `runtime/database.sqlite` |
| Testes | Codeception | unit + functional |
| Banco (testes) | SQLite separado | `config/test_db.php` |

---

## Anti-padrões (NUNCA fazer)

- **Unificar os dois estilos de service** — Query Builder e ActiveRecord coexistem intencionalmente para estudo comparativo
- **CORS global em `config/web.php`** — configurar no `behaviors()` de cada controller REST
- **`NOW()` ou MySQL-specific SQL** — banco é SQLite; usar `datetime('now')` ou `date('Y-m-d H:i:s')`
- **Salvar o model sem validar antes** — controller valida via `$model->validate()` antes de delegar ao service
- **Service retornar sem try/catch** — writes devem ter transação + rethrow como `ServerErrorHttpException`

---

## Envelope de API (imutável)

```php
// Sucesso
return ['success' => true, 'type' => 'success', 'data' => $result];

// Erro
Yii::$app->response->statusCode = 400;
return ['success' => false, 'type' => 'exception', 'message' => $e->getMessage()];
```

Status codes: **200** (GET/PUT), **201** (POST create), **204** (DELETE), **400** (validação), **500** (erro interno).

---

## Arquitetura do backend

```
config/web.php (urlManager + UrlRule)
  → Controller (CORS, validação via Model, delegação)
  → Service (Query Builder primário | ActiveRecord espelho)
  → Model (rules, behaviors, relacionamentos)
  → SQLite
```

### Request flow completo

```
HTTP Request
  → urlManager: casa rota → identifica controller/action
  → Controller::behaviors(): CORS preflight
  → Controller::actionXxx(): carrega body, instancia Model apenas para validar
  → Model::validate(): regras de required, email, unique
  → Service::method(): Query Builder com transação
  → Controller: monta envelope { success, type, data }
  → contentNegotiator: serializa para JSON
```

---

## Dois estilos de service (invariante)

| Sufixo | Estilo | Propósito |
|---|---|---|
| `findAll`, `create`, `update` | Query Builder / createCommand | Implementação primária |
| `findAll_new`, `create_new` | ActiveRecord | Espelho educacional comparativo |

**Regra**: ao adicionar feature nova, usar Query Builder (sem sufixo) como primário. Criar o espelho `_new` se o objetivo for comparativo.

---

## CORS (por controller)

```php
'corsFilter' => [
    'class' => \yii\filters\Cors::class,
    'cors' => [
        'Origin' => ['http://localhost:5500'],
        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'Access-Control-Allow-Credentials' => false,
    ],
],
```

Deve ser o **primeiro** item em `behaviors()`, antes de authenticator e contentNegotiator.

---

## Timestamps — SQLite-específico

```php
// Em behaviors / TimestampBehavior
'value' => new Expression("datetime('now')")

// No service — INSERT
'created_at' => new Expression("datetime('now')")
'updated_at' => new Expression("datetime('now')")

// No service — UPDATE
'updated_at' => date('Y-m-d H:i:s')
```

---

## DI via constructor

Services são resolvidos automaticamente pelo container do Yii2 por type hint no construtor. Não registrar explicitamente no container.

```php
public function __construct(UserService $userService, $id, $module, $config = [])
{
    $this->userService = $userService;
    parent::__construct($id, $module, $config);
}
```

---

## Roteamento REST

Rotas padrão via `UrlRule` em `config/web.php`. Actions não-REST precisam de short-form rule **antes** da `UrlRule`:

```php
// config/web.php — ANTES do UrlRule
['class' => 'yii\web\UrlRule', 'pattern' => 'user-api/archive/<id:\d+>', 'route' => 'user-api/archive'],
```

---

## Convenções

- Comentários são em **português** — manter esse tom nos arquivos existentes
- `PDO::ATTR_STRINGIFY_FETCHES` desabilitado — numéricos retornam como int/float
- `POST /user-api` retorna string, não o recurso criado — limitação conhecida
- Banco de testes é SQLite separado (`tests/bin/yii migrate` para aplicar)

---

## Recursos implementados

| Recurso | Controller | Rotas REST |
|---|---|---|
| `user-api` | `UserApiController` | GET/POST `/user-api`, GET/PUT/DELETE `/user-api/:id` |
| `user-config` | `UserConfigController` | GET/POST `/user-config`, PUT/DELETE `/user-config/:id` |
| `user-profile` | `UserProfileController` | GET/POST `/user-profile`, DELETE `/user-profile/:id` |
| `user-profile-setting` | `UserProfileSettingController` | GET/POST `/user-profile-setting`, PUT/DELETE `/user-profile-setting/:id` |

Detalhes completos em `ai/docs/<recurso>.md`.
