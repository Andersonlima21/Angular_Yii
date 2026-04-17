# Workflow: Testing

## Matriz de testes por tipo

| Tipo | Onde | Comando |
|---|---|---|
| Sintaxe JS (frontend) | CI / local | `node --check app/**/*.js` |
| Unit (backend models) | `yii2-app-basic/` | `vendor/bin/codecept run unit` |
| Functional (backend API) | `yii2-app-basic/` | `vendor/bin/codecept run functional` |
| Manual (frontend + integração) | Browser | backend em :8080, frontend em :5500 |

## O que testar manualmente (frontend)

Para cada feature, cobrir:
1. **Happy path** — fluxo principal do usuário
2. **Erro de API** — o que aparece quando o backend retorna `success: false`
3. **Campo obrigatório vazio** — validação de formulário
4. **Reload da página** — estado é restaurado corretamente via URL/resolve

## Quando NÃO escrever teste

- Componentes puramente visuais sem lógica
- Código trivial de passagem (getters simples)
- Workarounds temporários documentados para remoção futura
