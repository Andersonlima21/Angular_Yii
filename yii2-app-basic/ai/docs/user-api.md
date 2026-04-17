# user-api

**Controller**: `app\controllers\UserApiController`
**Base URL**: `http://localhost:8080/user-api`
**Service**: `app\services\UserService`
**Model**: `app\models\UserApi` (tabela `users`)

## Operações

### GET /user-api
Lista todos os usuários.

**Response**:
```json
{ "success": true, "type": "success", "data": [{ "id": 1, "name": "...", "email": "..." }] }
```

### GET /user-api/:id
Retorna um usuário com configs e profiles embutidos.

**Response**:
```json
{
  "success": true,
  "type": "success",
  "data": {
    "id": 1, "name": "...", "email": "...", "ativo": 1,
    "created_at": "YYYY-MM-DD HH:MM:SS",
    "updated_at": "YYYY-MM-DD HH:MM:SS",
    "configs": [{ "id": 1, "user_id": 1, "key": "...", "value": "..." }],
    "profiles": [{ "id": 1, "user_id": 1, "phone": "...", "bio": "..." }]
  }
}
```

### POST /user-api
Cria um novo usuário.

**Payload**:
```json
{ "name": "...", "email": "..." }
```

**Response** (atenção: retorna string, não o recurso criado):
```json
{ "success": true, "type": "success", "data": "User created successfully" }
```
**Status**: 201

> **Workaround no frontend**: após POST, faz `findAll` + filtra por email (único) para obter o id.

### PUT /user-api/:id
Atualiza um usuário.

**Payload**: mesmos campos do POST (parcial aceito).
**Response**: usuário atualizado com mesma shape do GET /:id.

### DELETE /user-api/:id
Remove um usuário.
**Status**: 204 (sem body).

### GET /user-api/v2 *(benchmark)*
Mesma funcionalidade do GET /user-api mas via ActiveRecord. Apenas para estudo comparativo.

## Validações (Model rules)

- `name`: required
- `email`: required, email, unique

## Observações

- `email` é único — usado como chave de lookup pós-create pelo frontend
- `configs` e `profiles` são embutidos apenas no GET /:id
- CORS configurado no `behaviors()` do `UserApiController`
- TimestampBehavior usa `new Expression("datetime('now')")` — SQLite-específico
