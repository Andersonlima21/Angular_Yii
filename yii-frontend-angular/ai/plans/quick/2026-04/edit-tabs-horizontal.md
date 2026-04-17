# Quick Plan: edit-tabs-horizontal

**Data**: 2026-04-17
**Agente**: `frontend-developer`
**Status**: [x] Execution | [x] Done

## Objetivo
Substituir `nav-pills` verticais por `nav-tabs` horizontais no topo da tela de edição de usuário, com o conteúdo da tab renderizado abaixo.

## Decisão registrada
Optei por `nav-tabs` horizontais (revertendo a decisão de pills verticais da feature anterior) porque com apenas 3 tabs o padrão horizontal é mais intuitivo para o usuário final e alinha com a expectativa visual de "abas acima do conteúdo". Atualizar `standards/frontend/components.md` para refletir a mudança.

## Critérios de aceite
- [x] `nav-tabs` horizontais exibidas acima do `ui-view` em `user-edit.html`
- [x] `ui-sref-active="active"` mantido — aba ativa destacada corretamente
- [x] Layout de 2 colunas (col-md-2 pills + col-md-10 conteúdo) removido
- [x] `node --check app/**/*.js` limpo (sem toque em JS)

## Tarefas

### T1 — Refatorar `user-edit.html`
**O que faz**: Remove o `row g-0` com col-md-2/col-md-10, substitui `nav-pills flex-column` por `ul.nav.nav-tabs` horizontal acima do `ui-view`.
**Arquivo**: `app/components/user-edit/user-edit.html`

Layout alvo:
```
[ Header: nome do usuário + botão Voltar ]
[ Breadcrumb ]
[ Tab Info | Tab Configs (badge) | Tab Profile (ícone) ]  ← nav-tabs horizontal
─────────────────────────────────────────────────────────
[ conteúdo da tab ativa — ui-view ]
```

### T2 — Limpar `app/app.css`
**O que faz**: Remove regras de `.nav-pills` se não forem mais usadas em nenhuma outra tela.
**Arquivo**: `app/app.css`

### T3 — Atualizar standard
**O que faz**: Reverte a seção "Nav-pills verticais" em `components.md` para documentar `nav-tabs` como padrão para nested states com ≤4 tabs.
**Arquivo**: `ai/standards/frontend/components.md`

### T4 — Verificação
```bash
node --check app/**/*.js
grep "nav-pills" app/components/user-edit/user-edit.html   # deve retornar vazio
grep "nav-tabs" app/components/user-edit/user-edit.html    # deve ter match
grep "nav-pills" app/app.css                               # verificar se ainda usado
```

## Handoff
**Próxima ação**: Executar T1 em `user-edit.html`.
**Não muda**: estados UI-Router, `ui-sref`, controllers, services.
**Risco**: verificar se `.nav-pills` em `app.css` é usado em outro lugar antes de remover (grep primeiro).
