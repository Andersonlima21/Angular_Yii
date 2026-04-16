# Yii Frontend (AngularJS 1.x + Bootstrap 5)

SPA de estudo que consome a API REST do projeto vizinho `../yii2-app-basic`.

## Stack

Todas as libs vêm de CDN — não há `npm install` nem etapa de build:

- AngularJS 1.8.3
- Angular UI-Router 1.0.30 (rotas aninhadas para as tabs)
- Bootstrap 5.3.3 + Bootstrap Icons 1.11.3

## Como rodar

### 1. Suba o backend primeiro

```bash
cd ../yii2-app-basic
php yii serve        # API em http://localhost:8080
```

### 2. Suba o frontend

O app **não pode ser aberto via `file://`** porque o UI-Router carrega templates por `templateUrl`. Use qualquer servidor estático a partir do diretório `yii-frontend-angular`:

```bash
python -m http.server 5500   # http://localhost:5500

# ou PHP
php -S localhost:5500

# ou VS Code: extensão Live Server
```

> Se mudar a porta do Yii, ajuste `app/services/apiConfig.js` (`API_BASE_URL`).

## Fluxo de telas

| Rota                            | Tela                                                     |
|---------------------------------|----------------------------------------------------------|
| `/users`                        | Listagem com filtro client-side por nome / email / ativo |
| `/users/new`                    | Criação (name + email); redireciona para edição após POST |
| `/users/:id/edit/info`          | Edita name/email (PUT)                                   |
| `/users/:id/edit/configs`       | Lista configs aninhadas + form para criar nova           |
| `/users/:id/edit/profiles`      | Lista profiles aninhados + form para criar novo          |
| `/users/:id/edit/settings`      | Cria/atualiza/remove setting 1:1 com profile             |

## Notas sobre a API

- Envelope padrão: `{ success, type, data | message }`. Os services Angular já desempacotam o `data` — controllers nunca veem o envelope.
- `POST /user-api` retorna apenas uma string de sucesso, sem o id do recurso criado. Por isso `UserCreateCtrl` faz um `findAll` logo após e localiza o usuário pelo email (unique) para redirecionar para a edição.
- `GET /user-api/:id` devolve o usuário com `configs` e `profiles` aninhados. As tabs de edição leem desse objeto cacheado em vez de fazer chamadas separadas.
- `user-config`, `user-profile` e `user-profile-setting` têm algumas actions marcadas como `// TODO` no backend (ex.: delete/update de config). O frontend só chama endpoints que estão implementados.

## CI

O pipeline `frontend-ci.yml` roda `node --check` em todos os arquivos `.js` do diretório `app/` para garantir que nenhuma sintaxe inválida chegue ao repositório.
