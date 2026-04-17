# Quick Plan: backend-filters-integration

**Data**: 2026-04-17
**Agente**: `frontend-developer`
**Status**: [x] Execution | [x] Done

## Objetivo
Integrar os 5 novos parâmetros de filtro do backend (`busca`, `campos[]`, `ordenar_por`+`direcao`, `coluna`, `limite`) na listagem de usuários.

## Decisões registradas
- Optei por manter `filter.q` (client-side) e `busca` (server-side) coexistindo — demonstram estratégias distintas de busca sem conflito
- Optei por manter `ordenacao` existente (usort) ao lado dos novos `ordenar_por`+`direcao` — params diferentes no backend, funções PHP distintas
- `coluna` cria um novo `modo === 'coluna'` (array plano de strings/valores), análogo ao modo `resumo`
- `campos[]` enviado como array; ações na tabela condicionadas a `u.id !== undefined`

## Critérios de aceite
- [x] `busca`, `limite`, `ordenar_por`+`direcao` enviados como query params em `load()` quando preenchidos
- [x] `campos[]` enviado como array de strings; tabela adapta colunas dinamicamente (sem ações se `id` ausente)
- [x] `coluna` ativa novo modo de exibição mostrando array plano (lista de badges/valores)

## Tarefas

### T1 — Atualizar `user-list.component.js`
**O que faz**: Adiciona `busca`, `limite`, `ordenar_por`, `direcao`, `campos` (array), `coluna` ao `$ctrl.filter`; passa os novos params em `load()`; detecta modo `coluna` (data[0] é primitivo).
**Arquivo**: `app/components/user-list/user-list.component.js`

### T2 — `user-list.html` — Seção Campos (novos controles)
**O que faz**: Adiciona input `busca` (server-side search, ng-change → aplicarFiltros), selects `ordenar_por`+`direcao` na mesma linha, input `limite`.
**Arquivo**: `app/components/user-list/user-list.html`

### T3 — `user-list.html` — Seção Opções (coluna + campos)
**O que faz**: Adiciona select `coluna` (vazio = desativado; opções: id, name, email, is_active, created_at, updated_at) e checkboxes `campos[]` para projeção.
**Arquivo**: `app/components/user-list/user-list.html`

### T4 — `user-list.html` — Novo modo `coluna`
**O que faz**: Adiciona bloco `ng-if="!$ctrl.loading && $ctrl.modo === 'coluna'"` mostrando cada item do array plano como badge/li, com label do campo extraído.
**Arquivo**: `app/components/user-list/user-list.html`

### T5 — Verificação
```bash
node --check app/**/*.js
grep "busca"      app/components/user-list/user-list.component.js
grep "coluna"     app/components/user-list/user-list.component.js
grep "ordenar_por" app/components/user-list/user-list.html
```

## Handoff
**Próxima ação**: Executar T1 em `user-list.component.js`.
**Não muda**: `userService.js` (já aceita params livres), estados UI-Router, outros componentes.
**Risco**: `campos[]` sem `id` quebra ações de editar/excluir — condicionar com `ng-if="u.id !== undefined"`.
