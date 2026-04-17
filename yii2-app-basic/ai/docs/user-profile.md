# user-profile

**Controller**: `app\controllers\UserProfileController`
**Base URL**: `http://localhost:8080/user-profile`
**Model**: `app\models\UserProfile` (tabela `user_profiles`)

## Operações

### GET /user-profile
Lista todos os profiles.

**Response**:
```json
{
  "success": true, "type": "success",
  "data": [{ "id": 1, "user_id": 1, "phone": "...", "birth_date": "...", "bio": "...", "avatar_url": "..." }]
}
```

### POST /user-profile
Cria um novo profile para um usuário.

**Payload**:
```json
{ "user_id": 1, "phone": "(11) 99999-9999", "birth_date": "1990-01-01", "bio": "...", "avatar_url": "..." }
```
**Status**: 201

### DELETE /user-profile/:id
Remove um profile.
**Status**: 204.

## Observações

- Profiles são retornados embutidos no `GET /user-api/:id` como array `profiles`
- Um usuário pode ter múltiplos profiles (mas a UI trabalha com um por vez)
- Cada profile pode ter um `setting` 1:1 (ver `user-profile-setting.md`)
- `phone` é formatado pela diretiva `phoneMask` no frontend: `(XX) XXXXX-XXXX`
- Sem endpoint de update (PUT) implementado no projeto atual
