# Quick Plan: filter-sections

**Data**: 2026-04-17
**Agente**: `frontend-developer`
**Status**: [x] Execution | [x] Done

## Objetivo
Reorganizar o painel de filtros da listagem em duas seções visuais distintas: "Campos" (selects e inputs) e "Opções" (checkboxes).

## Decisão registrada
Optei por dividir em **Campos** (Status, Ordenação, Agrupar em lotes) e **Opções** (Inverter ordem, Ocultar domínio, Paginar, Mostrar resumo) porque o critério natural é tipo de controle — selects/inputs são configuração de valor, checkboxes são toggles de comportamento.

## Critérios de aceite
- [x] Seção "Campos" agrupa: Status (select), Ordenação (select), Agrupar em lotes (number input)
- [x] Seção "Opções" agrupa: Inverter ordem, Ocultar domínio, Paginar, Mostrar resumo (checkboxes)
- [x] Separador visual entre seções (título + `<hr>` ou `<small class="text-muted">`)
- [x] `node --check app/**/*.js` limpo (sem toque em JS)

## Tarefas

### T1 — Reorganizar `user-list.html` (filtros)
**O que faz**: Reestrutura o `div.row.g-3` do painel de filtros em dois blocos com cabeçalho de seção.
**Arquivo**: `app/components/user-list/user-list.html`

Layout alvo:
```
[ Painel de filtros ]
  [ Busca client-side + Recarregar ]  ← sem mudança
  ─────────────────────────────────
  Campos
    [ Status ] [ Ordenação ] [ Agrupar em lotes ]
  ─────────────────────────────────
  Opções
    [ ] Inverter ordem   [ ] Ocultar domínio
    [ ] Paginar (+ input itens/pág se ativo)   [ ] Mostrar resumo
```

### T2 — Verificação
**O que faz**: Roda grep + node --check + teste visual no browser.
```bash
node --check app/**/*.js
grep "row g-3" app/components/user-list/user-list.html
```

## Handoff
**Próxima ação**: Executar T1 em `user-list.html`.
**Não muda**: JS, services, lógica de filtros, `$ctrl.aplicarFiltros()`.
