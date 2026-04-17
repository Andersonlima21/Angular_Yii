# user-profile-setting

**Controller**: `app\controllers\UserProfileSettingController`
**Base URL**: `http://localhost:8080/user-profile-setting`
**Model**: `app\models\UserProfileSetting` (tabela `user_profile_settings`)

## Operações

### GET /user-profile-setting
Lista todas as settings.

### GET /user-profile-setting/:id
Retorna uma setting específica.

**Response**:
```json
{ "success": true, "type": "success", "data": { "id": 1, "profile_id": 1, "theme": "...", "language": "..." } }
```

### POST /user-profile-setting
Cria uma nova setting (quando ainda não existe para o profile).

**Payload**:
```json
{ "profile_id": 1, "theme": "dark", "language": "pt-BR" }
```
**Status**: 201

### PUT /user-profile-setting/:id
Atualiza uma setting existente.

**Payload**: campos parciais aceitos.

### DELETE /user-profile-setting/:id
Remove uma setting.
**Status**: 204.

## Lógica de upsert (workaround obrigatório no frontend)

Relação 1:1 com profile. O backend **não faz upsert automático**. O frontend DEVE verificar se já existe:

```javascript
if (setting && setting.id) {
    // PUT /user-profile-setting/:id
} else {
    // POST /user-profile-setting
}
```

## Observações

- Settings NÃO são embutidas no `GET /user-api/:id` — precisam ser carregadas separadamente
- Tab "Settings" existe no frontend (`app/components/tab-settings/`) mas ainda não está wired em `app.js`
- DELETE existe no backend mas não implementado no frontend
