# user-profile-setting

**Base URL**: `http://localhost:8080/user-profile-setting`

## Operações

### GET /user-profile-setting
Lista todas as settings.

### GET /user-profile-setting/:id
Retorna uma setting específica.

**Response**:
```json
{ "success": true, "type": "success", "data": { "id": 1, "profile_id": 1, "key": "...", "value": "..." } }
```

### POST /user-profile-setting
Cria uma nova setting (quando ainda não existe para o profile).

**Payload**:
```json
{ "profile_id": 1, "key": "notifications", "value": "true" }
```

### PUT /user-profile-setting/:id
Atualiza uma setting existente (quando já existe para o profile).

## Lógica de upsert (workaround obrigatório)

Relação 1:1 com profile. O frontend DEVE verificar se já existe antes de decidir:

```js
if (setting && setting.id) {
  // PUT /user-profile-setting/:id
} else {
  // POST /user-profile-setting
}
```

Nunca fazer POST se já existe — o backend não faz upsert automático.

## Observações

- Sem DELETE no frontend
- Settings não são embutidas no GET /user-api/:id — precisam ser carregadas separadamente se necessário
