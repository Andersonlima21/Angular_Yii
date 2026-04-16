# Yii Frontend (AngularJS 1.x + Bootstrap 5)

Frontend de estudo que consome a API REST do projeto vizinho `../yii2-app-basic`.

Stack — todas as libs vêm de CDN (sem `npm install`):

- AngularJS 1.8.3
- Angular UI-Router 1.0.30 (rotas aninhadas para as tabs)
- Bootstrap 5.3.3 + Bootstrap Icons 1.11.3

## Como rodar

Em **dois terminais**:

### 1. Backend Yii (porta 8080)

```bash
cd ../yii2-app-basic
php yii serve
```

A API fica em `http://localhost:8080`. CORS já está habilitado nos 4 controllers REST.

### 2. Frontend (qualquer servidor estático)

Não pode ser aberto via `file://` porque o UI-Router carrega templates por `templateUrl`. Use um dos abaixo a partir do diretório `yii-frontend-angular`:

```bash
# Python 3
python -m http.server 5500

# ou PHP
php -S localhost:5500

# ou VS Code: extensão Live Server
```

Acesse `http://localhost:5500/`.

> Se mudar a porta do Yii, ajuste `app/services/apiConfig.js` (`API_BASE_URL`).

## Fluxo das telas

1. **`/users`** — listagem com filtro por nome/email/ativo (filtro client-side).
2. **`/users/new`** — formulário de criação (name + email). Após o POST o app busca o usuário recém-criado pelo email (unique) e redireciona direto para a tela de edição na aba **Info**.
3. **`/users/:id/edit/info`** — edita name/email do usuário (PUT).
4. **`/users/:id/edit/configs`** — lista as configs do user (vêm aninhadas em `GET /user-api/:id`) + form para criar nova config.
5. **`/users/:id/edit/profiles`** — lista os profiles do user + form para criar novo profile.
6. **`/users/:id/edit/settings`** — escolhe um profile do user e cria/atualiza/remove o setting (1:1 com profile).

## Notas sobre a API

- Envelope padrão de resposta: `{ success, type, data | message }`. Os services do Angular já desempacotam o `data`.
- `POST /user-api` retorna apenas uma string de sucesso, sem o id do recurso criado — por isso `UserCreateCtrl` faz um `findAll` em seguida e localiza o user por email.
- `user-config`, `user-profile` e `user-profile-setting` têm várias actions ainda como `// TODO` no backend (delete/update de config, findById de profile, findAll/findById de setting). O frontend só chama o que está implementado de fato.
