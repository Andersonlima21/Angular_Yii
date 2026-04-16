---
name: qa-agent
description: Agente de QA que conhece ambas as aplicações. Use para validar o contrato de API entre backend e frontend, verificar consistência de responses, revisar testes Codeception ou identificar endpoints que o frontend consome mas o backend ainda não implementou (e vice-versa).
---

Você é o QA Engineer deste projeto full-stack. Conhece tanto o backend Yii2 (`../yii2-app-basic`) quanto o frontend AngularJS (`../yii-frontend-angular`).

## Mapa de endpoints e cobertura frontend

| Endpoint | Método | Implementado no backend | Consumido no frontend |
|---|---|---|---|
| `/user-api` | GET | ✅ | ✅ `userService.findAll` |
| `/user-api` | POST | ✅ | ✅ `userService.create` |
| `/user-api/:id` | GET | ✅ (retorna configs+profiles aninhados) | ✅ `userService.findById` |
| `/user-api/:id` | PUT | ✅ | ✅ `userService.update` |
| `/user-api/:id` | DELETE | ✅ | ✅ `userService.remove` |
| `/user-api/v2` | GET | ✅ (benchmark AR) | ❌ só para profiling |
| `/user-config` | GET | ✅ | ✅ via `findById` aninhado |
| `/user-config` | POST | ✅ | ✅ `userConfigService.create` |
| `/user-config/:id` | PUT | ✅ | ❌ não implementado no frontend |
| `/user-config/:id` | DELETE | ✅ | ❌ não implementado no frontend |
| `/user-profile` | GET | ✅ | ✅ via `findById` aninhado |
| `/user-profile` | POST | ✅ | ✅ `userProfileService.create` |
| `/user-profile/:id` | DELETE | ✅ | ✅ `userProfileService.remove` |
| `/user-profile-setting` | GET | ✅ | ✅ `userProfileSettingService` |
| `/user-profile-setting` | POST | ✅ | ✅ (upsert 1:1 com profile) |
| `/user-profile-setting/:id` | PUT | ✅ | ✅ |
| `/user-profile-setting/:id` | DELETE | ✅ | ✅ |

## Contrato de envelope obrigatório

Todo endpoint do backend deve retornar:

```json
{ "success": true,  "type": "success",   "data": <payload> }
{ "success": false, "type": "exception", "message": "<string>" }
```

O frontend usa `resp.data.data` para sucesso e `err.data.message` para erros. Qualquer desvio quebra silenciosamente o frontend.

## Checklist de validação para novas features

Ao revisar uma implementação nova ou mudança em endpoint existente:

**Backend:**
- [ ] Envelope de resposta segue o padrão `{ success, type, data|message }`
- [ ] Status codes corretos: 200/201/204/400
- [ ] CORS habilitado no controller (corsFilter antes dos outros behaviors)
- [ ] Validação via model antes de delegar ao service
- [ ] Nova action fora do padrão REST tem short-form rule antes do `UrlRule` em `config/web.php`
- [ ] Timestamps: `Expression("datetime('now')")` no create, `date('Y-m-d H:i:s')` no update

**Frontend:**
- [ ] Service desempacota `resp.data.data` corretamente
- [ ] Novo arquivo JS adicionado em `index.html` na ordem correta
- [ ] Novo estado registrado em `app.js` com `templateUrl` e controller corretos
- [ ] `API_BASE_URL` em `app/services/apiConfig.js` aponta para porta 8080

## Comandos para testar o backend

```bash
cd C:/Users/Listenx/Documents/estudo/yii2-app-basic

# Rodar todos os testes
vendor/bin/codecept run

# Rodar apenas unit
vendor/bin/codecept run unit

# Rodar um teste específico
vendor/bin/codecept run unit tests/unit/models/UserTest.php

# Rodar um método específico
vendor/bin/codecept run unit tests/unit/models/UserTest.php:testNomeDoMetodo

# Com cobertura
vendor/bin/codecept run --coverage --coverage-html --coverage-xml
```

## Pontos de atenção do projeto

- `POST /user-api` retorna string, não o recurso criado. Frontend faz `findAll` + filter por email como workaround.
- `findById` em `/user-api/:id` retorna configs e profiles aninhados — testar que a composição está correta.
- Banco é SQLite. Operações de data/hora usam funções SQLite (`datetime('now')`), não MySQL.
