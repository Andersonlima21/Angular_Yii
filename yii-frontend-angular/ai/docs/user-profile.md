# user-profile

**Base URL**: `http://localhost:8080/user-profile`

## Operações

### GET /user-profile
Lista todos os profiles.

### GET /user-profile/:id
Retorna um profile específico.

**Response**:
```json
{ "success": true, "type": "success", "data": { "id": 1, "user_id": 1, "type": "personal", "bio": "..." } }
```

### POST /user-profile
Cria um novo profile para um usuário.

**Payload**:
```json
{ "user_id": 1, "type": "personal", "bio": "..." }
```

### PUT /user-profile/:id
Atualiza um profile existente.

### DELETE /user-profile/:id
Remove um profile.

## Observações

- Profiles são carregados embutidos em `GET /user-api/:id` (array `profiles`)
- Cada profile pode ter um `user-profile-setting` associado (relação 1:1)
- O frontend lê profiles via `userEditContext`, não via chamada direta
