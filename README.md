# Angular_Yii

Projeto de estudo full-stack: API REST em **Yii 2** consumida por um SPA em **AngularJS 1.x**.

## Stack

| Camada    | Tecnologia                                      |
|-----------|-------------------------------------------------|
| Backend   | PHP 7.4+, Yii 2, SQLite, Codeception            |
| Frontend  | AngularJS 1.8.3, UI-Router, Bootstrap 5 (CDN)  |

Sem `npm install` no frontend — todas as libs vêm de CDN.

## Como rodar

Dois terminais:

```bash
# Terminal 1 — API (http://localhost:8080)
cd yii2-app-basic
composer install
php yii migrate
php yii serve

# Terminal 2 — SPA (http://localhost:5500)
cd yii-frontend-angular
python -m http.server 5500
```

Acesse o app em `http://localhost:5500`.

## Estrutura

```
yii2-app-basic/          # API REST (Yii 2)
  controllers/           # UserApiController, UserConfigController, ...
  services/              # Lógica de negócio (Query Builder + ActiveRecord lado a lado)
  models/                # ActiveRecord (UserApi, UserConfig, UserProfile, ...)
  migrations/            # Schema do SQLite
  tests/                 # Codeception: unit + functional

yii-frontend-angular/    # SPA (AngularJS 1.x)
  app/
    services/            # Wrappers HTTP que desempacotam o envelope da API
    controllers/         # Controllers das telas
    views/               # Templates HTML (carregados pelo UI-Router)
    filters/             # Filtro de data
    directives/          # Máscara de telefone
```

## CI

| Workflow            | O que verifica                                      |
|---------------------|-----------------------------------------------------|
| `backend-ci.yml`    | Sintaxe PHP, dependências, migrações, testes        |
| `frontend-ci.yml`   | Sintaxe JS (`node --check`) em todos os arquivos    |
