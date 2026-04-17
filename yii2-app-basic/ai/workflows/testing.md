# Workflow: Testing

## Matriz de testes por tipo

| Tipo | Onde | Comando |
|---|---|---|
| Unit (models/services) | `tests/unit/` | `vendor/bin/codecept run unit` |
| Functional (API endpoints) | `tests/functional/` | `vendor/bin/codecept run functional` |
| Com cobertura | `tests/` | `vendor/bin/codecept run --coverage --coverage-html` |
| Teste específico | `tests/unit/` | `vendor/bin/codecept run unit tests/unit/models/UserTest.php:testNome` |
| Banco de testes | `tests/` | `tests/bin/yii migrate` para aplicar migrations |

## O que testar (unit)

Para cada model/service novo ou modificado:
1. **Happy path** — comportamento esperado com dados válidos
2. **Validação** — campos obrigatórios, unique, formatos
3. **Edge cases** — id inexistente, dados parciais no update

## O que testar (functional)

Para cada endpoint novo:
1. **Status code correto** — 200/201/204/400
2. **Envelope** — `{ success: true, type: 'success', data: {...} }`
3. **CORS headers** — `Access-Control-Allow-Origin` presente
4. **Erro de validação** — body com campo faltando retorna 400

## Banco de testes

SQLite separado em `config/test_db.php`. Aplicar migrations antes de rodar:

```bash
tests/bin/yii migrate
vendor/bin/codecept run
```

## Quando NÃO escrever teste

- Código trivial de passagem (getters simples no model)
- Workarounds temporários documentados para remoção futura
- Espelhos `_new` de ActiveRecord (já cobertos pelo primário)
