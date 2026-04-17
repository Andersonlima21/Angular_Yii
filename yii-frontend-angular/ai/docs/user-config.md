# user-config

**Base URL**: `http://localhost:8080/user-config`

## Operações

### GET /user-config
Lista todas as configs (geralmente filtrado por user_id via query param).

### GET /user-config/:id
Retorna uma config específica.

**Response**:
```json
{ "success": true, "type": "success", "data": { "id": 1, "user_id": 1, "key": "theme", "value": "dark" } }
```

### POST /user-config
Cria uma nova config para um usuário.

**Payload**:
```json
{ "user_id": 1, "key": "theme", "value": "dark" }
```

### PUT /user-config/:id
Atualiza uma config existente.

### DELETE /user-config/:id
Remove uma config. **Existe no backend, mas não é chamado pelo frontend atualmente.**

## Observações

- Configs são carregadas embutidas em `GET /user-api/:id` (array `configs`)
- O frontend lê configs via `userEditContext`, não via chamada direta a este endpoint
- Sem lógica de upsert — cada config tem id próprio
