# Standard: Controllers — Padrões Yii2

Padrões obrigatórios para todo controller REST do projeto.

---

## Herança

Todos os controllers REST herdam de:

```php
class UserApiController extends \yii\rest\ActiveController
// ou
class UserApiController extends \yii\rest\Controller
```

---

## behaviors() — ordem obrigatória

```php
public function behaviors()
{
    $behaviors = parent::behaviors();

    // 1. CORS — DEVE ser o primeiro
    $behaviors['corsFilter'] = [
        'class' => \yii\filters\Cors::class,
        'cors' => [
            'Origin' => ['http://localhost:5500'],
            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'Access-Control-Allow-Credentials' => false,
        ],
    ];

    // 2. Authenticator (futuro)
    // 3. ContentNegotiator (já declarado pelo parent)

    return $behaviors;
}
```

---

## Validação antes do service

Instanciar o Model **apenas para validar** — não para salvar:

```php
$model = new UserApi();
$model->load(Yii::$app->request->bodyParams, '');
if (!$model->validate()) {
    $messages = [];
    foreach ($model->errors as $field => $errors) {
        $messages[] = implode(', ', $errors);
    }
    throw new \yii\web\HttpException(400, implode(' | ', $messages));
}
// delegar ao service
$result = $this->userService->create(Yii::$app->request->bodyParams);
```

---

## Envelope de resposta

```php
// Sucesso
return ['success' => true, 'type' => 'success', 'data' => $result];

// Erro controlado
Yii::$app->response->statusCode = 400;
return ['success' => false, 'type' => 'exception', 'message' => $e->getMessage()];
```

| Cenário | Status |
|---|---|
| GET lista / item | 200 |
| POST create | 201 |
| PUT update | 200 |
| DELETE | 204 (sem body) |
| Validação falhou | 400 |
| Erro interno | 500 |

---

## DI via constructor

```php
private UserService $userService;

public function __construct(UserService $userService, $id, $module, $config = [])
{
    $this->userService = $userService;
    parent::__construct($id, $module, $config);
}
```

O Yii2 DI resolve por type hint — sem registro explícito necessário.

---

## Actions não-REST

Qualquer `actionFoo` fora do CRUD padrão precisa de short-form rule em `config/web.php` **antes** do `UrlRule`:

```php
// config/web.php
'rules' => [
    // Primeiro as regras específicas
    ['class' => 'yii\web\UrlRule', 'pattern' => 'user-api/foo', 'route' => 'user-api/foo'],
    // Depois o UrlRule REST
    ['class' => 'yii\rest\UrlRule', 'controller' => ['user-api', 'user-config'], 'pluralize' => false],
],
```
