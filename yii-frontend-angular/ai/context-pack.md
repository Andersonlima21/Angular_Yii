# Context Pack — Regras Congeladas

> Carregado em TODA sessão. Máx 2-3 páginas. Mudanças requerem aprovação via `governance.md`.

---

## Stack

| Camada | Tecnologia | Versão |
|---|---|---|
| Frontend framework | AngularJS | 1.8.3 |
| Roteamento | UI-Router | 1.0.30 |
| CSS | Bootstrap | 5.3.3 |
| Backend | Yii2 REST | — |
| DB (dev) | SQLite | — |
| Entrega de libs | CDN-only | sem npm/bundler |

---

## Anti-padrões (NUNCA fazer)

- **Controller acessar `resp.data` direto** — sempre usar o service que desempacota
- **Abrir frontend via `file://`** — templateUrl quebra; usar servidor HTTP
- **Adicionar npm packages** — sem bundler; CDN-only
- **Inventar endpoint não documentado** — verificar `ai/docs/` ou perguntar
- **Child tab fazer chamada de API própria** — usar `userEditContext`

---

## Envelope de API (imutável)

```json
{ "success": true,  "type": "success",   "data": { ... } }
{ "success": false, "type": "exception", "message": "..." }
```

Services Angular desempacotam via `unwrap(resp)` → retornam `data` puro.
Controllers recebem `data`, nunca o envelope.
CORS: tratado por-controller no `behaviors()` do Yii2, não globalmente.

---

## Estrutura do frontend

```
app/
├── app.js                  # módulo yiiApp, $stateProvider
├── components/             # componentes com .component.js + .html
│   ├── tab-configs/
│   ├── tab-info/
│   ├── tab-profiles/
│   ├── tab-settings/       # existe, não wired em app.js ainda
│   ├── user-create/
│   ├── user-edit/
│   └── user-list/
├── controllers/            # controllers legados (não componentizados)
├── directives/
│   └── phoneMask.js        # máscara (XX) XXXXX-XXXX, valida 11 dígitos
├── filters/
│   └── sqlDate.js          # YYYY-MM-DD HH:MM:SS → dd/MM/yyyy HH:mm
├── services/
│   ├── apiConfig.js        # constante API_BASE_URL
│   ├── userService.js
│   ├── userConfigService.js
│   ├── userProfileService.js
│   ├── userProfileSettingService.js
│   └── userEditContext.js  # estado compartilhado entre tabs de edição
├── shared/
└── views/
```

---

## Estados UI-Router (app/app.js)

```
users                        → lista de usuários
newUser                      → formulário de criação
editUser                     → pai (resolve: userService.findById → userData)
  ├── editUser.info
  ├── editUser.configs
  ├── editUser.profiles
  └── editUser.settings      ← não registrado ainda
```

**`userEditContext`**: o pai (`userEdit` component) publica `user` e `reload()`. Tabs filhas leem daqui — nunca fazem chamadas de API próprias.

---

## Ordem de scripts em index.html

1. CDN (AngularJS, UI-Router, Bootstrap)
2. `app/app.js`
3. Filters
4. Services
5. Controllers / Components

Todo novo arquivo JS DEVE ser adicionado manualmente nesta ordem.

---

## Workarounds conhecidos (manter enquanto backend não mudar)

| Workaround | Motivo |
|---|---|
| Pós-create: `findAll` + filter por email | `POST /user-api` retorna string, sem id do recurso criado |
| `user-profile-setting`: verifica existência antes de POST vs PUT | Lógica de upsert 1:1 com profile |

---

## Convenções de código

- Comentários no backend são em **português** — manter esse tom
- JS sem TypeScript, sem transpiler — deve rodar no browser diretamente
- Verificação de sintaxe: `node --check app/**/*.js`
- Sem `console.log` de debug em commits

---

## Recursos de API disponíveis

| Recurso | Rota base | Observação |
|---|---|---|
| user-api | `/user-api` | dados embutidos: `configs[]`, `profiles[]` no GET /:id |
| user-config | `/user-config` | sem DELETE no frontend |
| user-profile | `/user-profile` | — |
| user-profile-setting | `/user-profile-setting` | upsert 1:1 com profile |

Detalhes completos em `ai/docs/<recurso>.md`.
