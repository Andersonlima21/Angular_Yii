# Standard: Services — Padrões Yii2

Padrões obrigatórios para a camada de service do projeto.

---

## Dois estilos (invariante de estudo)

| Método | Estilo | Regra |
|---|---|---|
| `findAll`, `findById`, `create`, `update`, `delete` | **Query Builder** | Implementação primária — usar em produção |
| `findAll_new`, `create_new`, etc. | **ActiveRecord** | Espelho educacional comparativo — não remover |

Ao adicionar feature nova: implementar no estilo Query Builder primeiro. Criar `_new` só se o objetivo for comparativo.

---

## Query Builder — template de read

```php
public function findAll(): array
{
    return Yii::$app->db->createCommand('SELECT * FROM users ORDER BY id DESC')
        ->queryAll();
}

public function findById(int $id): ?array
{
    $user = Yii::$app->db->createCommand('SELECT * FROM users WHERE id = :id', [':id' => $id])
        ->queryOne();

    if (!$user) {
        throw new \yii\web\NotFoundHttpException("User $id not found.");
    }

    $user['configs'] = Yii::$app->db->createCommand(
        'SELECT * FROM user_configs WHERE user_id = :id', [':id' => $id]
    )->queryAll();

    return $user;
}
```

---

## Query Builder — template de write (com transação)

```php
public function create(array $data): string
{
    $db = Yii::$app->db;
    $transaction = $db->beginTransaction();

    try {
        $db->createCommand()->insert('users', [
            'name'       => $data['name'],
            'email'      => $data['email'],
            'created_at' => new Expression("datetime('now')"),
            'updated_at' => new Expression("datetime('now')"),
        ])->execute();

        $transaction->commit();
        return 'User created successfully';
    } catch (\Exception $e) {
        $transaction->rollBack();
        throw new \yii\web\ServerErrorHttpException($e->getMessage());
    }
}

public function update(int $id, array $data): array
{
    $db = Yii::$app->db;
    $transaction = $db->beginTransaction();

    try {
        $db->createCommand()->update('users', [
            'name'       => $data['name'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id])->execute();

        $transaction->commit();
        return $this->findById($id);
    } catch (\Exception $e) {
        $transaction->rollBack();
        throw new \yii\web\ServerErrorHttpException($e->getMessage());
    }
}
```

---

## Timestamps — SQLite

```php
// CREATE — usar expressão SQLite
'created_at' => new Expression("datetime('now')")
'updated_at' => new Expression("datetime('now')")

// UPDATE — usar PHP date (ambos funcionam; date() é mais legível)
'updated_at' => date('Y-m-d H:i:s')
```

**Nunca** usar `NOW()` (MySQL) ou `time()` bruto em campos de timestamp.

---

## ActiveRecord — template espelho

```php
public function findAll_new(): array
{
    return UserApi::find()->orderBy(['id' => SORT_DESC])->asArray()->all();
}

public function create_new(array $data): UserApi
{
    $model = new UserApi();
    $model->load($data, '');
    if (!$model->save()) {
        throw new \yii\web\ServerErrorHttpException(
            implode(' | ', \yii\helpers\ArrayHelper::getColumn($model->errors, 0))
        );
    }
    return $model;
}
```

---

## Regras gerais

- Todo método de write usa `beginTransaction()` / `commit()` / `rollBack()`
- Erros internos relançados como `ServerErrorHttpException`
- Erros de negócio (not found) lançados como `NotFoundHttpException`
- Service **não** conhece o request — recebe arrays simples do controller
