# user-config

**Controller**: `app\controllers\UserConfigController`
**Base URL**: `http://localhost:8080/user-config`
**Model**: `app\models\UserConfig` (tabela `user_configs`)

## Operações

### GET /user-config
Lista todas as configs.

**Response**:
```json
{ "success": true, "type": "success", "data": [{ "id": 1, "user_id": 1, "key": "...", "value": "..." }] }
```

### POST /user-config
Cria uma nova config para um usuário.

**Payload**:
```json
{ "user_id": 1, "key": "theme", "value": "dark" }
```
**Status**: 201

### PUT /user-config/:id
Atualiza uma config existente.

**Payload**: `{ "key": "...", "value": "..." }` (parcial aceito).

### DELETE /user-config/:id
Remove uma config.
**Status**: 204.

## Observações

- Configs são pares chave-valor livres — sem schema fixo de chaves
- Retornadas embutidas no `GET /user-api/:id` como array `configs`
- PUT e DELETE existem no backend mas **não implementados no frontend** (gap conhecido)
- CORS configurado no `behaviors()` do `UserConfigController`
