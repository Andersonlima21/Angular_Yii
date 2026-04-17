# user-api

**Base URL**: `http://localhost:8080/user-api`

## Operações

### GET /user-api
Lista todos os usuários.

**Response**:
```json
{ "success": true, "type": "success", "data": [ { "id": 1, "name": "...", "email": "..." } ] }
```

### GET /user-api/:id
Retorna um usuário com configs e profiles embutidos.

**Response**:
```json
{
  "success": true,
  "type": "success",
  "data": {
    "id": 1,
    "name": "...",
    "email": "...",
    "configs": [ { "id": 1, "user_id": 1, "key": "...", "value": "..." } ],
    "profiles": [ { "id": 1, "user_id": 1, "type": "...", "bio": "..." } ]
  }
}
```

### POST /user-api
Cria um novo usuário.

**Payload**:
```json
{ "name": "...", "email": "...", "password": "..." }
```

**Response** (atenção: retorna string, não o recurso criado):
```json
{ "success": true, "type": "success", "data": "User created successfully" }
```

> **Workaround**: após o POST, o frontend faz `findAll` e filtra por email (único) para obter o id.

### PUT /user-api/:id
Atualiza um usuário.

**Payload**: mesmos campos do POST (parcial aceito).

### DELETE /user-api/:id
Remove um usuário. Não exposto no frontend atualmente.

## Observações

- `email` é único — usado como chave de lookup pós-create
- `configs` e `profiles` são embutidos apenas no GET /:id, não na listagem
- CORS configurado no `behaviors()` do `UserApiController`
