# AI Folder Guide

Este diretório é a **base de conhecimento** que o Claude Code (e outros agentes de IA) usam para trabalhar com eficiência neste codebase. Cada subpasta tem um papel bem definido — leia este guia antes de adicionar/mover conteúdo.

## Princípios

- **Context budget**: agentes carregam no máximo `context-pack.md` + **1 standard** + **1 skill/plano** por sessão
- **Frozen rules**: regras congeladas só mudam via governance (ver `governance.md`)
- **Patterns vs processes vs tasks**:
  - padrões estáveis → `standards/`
  - processos operacionais → `workflows/`
  - receitas executáveis → `skills/`

---

## Raiz

| Arquivo | Papel |
|---|---|
| `README.md` | **Entrypoint** — navegação, regra de context budget, índice de padrões. Claude lê esse arquivo primeiro. |
| `context-pack.md` | **Regras congeladas** carregadas em TODA sessão. Máx 2-3 páginas. Arquitetura, componentes, forms, API, styling, anti-padrões, naming. |
| `governance.md` | Protocolo de mudança de regras: quem aprova, como congelar/descongelar, matriz de ownership. |
| `FOLDER-GUIDE.md` | Este arquivo. |

---

## `/agents/` — Papéis de especialistas

Cada arquivo define um agente com contexto, responsabilidades e padrões específicos. Use quando precisar delegar tarefas complexas.

| Agente | Quando usar |
|---|---|
| `frontend-developer.md` | Trabalhar com o framework encotrado nas stacks, páginas, forms |
| `backend-integration.md` | Integrar com APIs, Server Actions, auth |
| `tech-lead.md` | Decisões arquiteturais, planejamento de features |
| `code-reviewer.md` | Revisão de PR, checklist de qualidade |
| `qa-engineer.md` | Casos de teste, edge cases, bug reports |
| `debugger.md` | Investigação de bugs, processo de debug |
| `performance-engineer.md` | Memoização, bundle, Core Web Vitals |
| `ui-ux-assistant.md` | Design system, acessibilidade, feedback |
| `documentation-writer.md` | Docs de features, APIs, how-tos |
| `devops.md` | Build, deploy, CI/CD, env vars |
| `handoffs.md` | Protocolo de handoff entre agentes |
| `README.md` | Catálogo e mapa task → agente |

---

## `/standards/` — Regras congeladas por domínio

Padrões estáveis organizados por camada. Agente carrega **1 por sessão** sob demanda.

### `/standards/domain/`
Arquitetura e regras de negócio.
- `architecture.md` — estrutura de pastas, fluxo de dados
- `tech-stack.md` — dependências e versões
- `features.md` — anatomia de um módulo de feature
- `lookups.md` — selects dinâmicos com cache

### `/standards/frontend/`
Camada de UI.
- `components.md` — catálogo de componentes compartilhados
- `forms.md` — Não definimos padrões, pois pode ser que o projeto novo não utilize React, neste caso o padrão será o que foi encontado
- `styling.md` — MUI v7, sx prop, grid de 8px ( os padrões são definidos pela varredura ok)
- `routing.md` — App Router, layouts, middleware ( os padrões são definidos pela varredura ok)
- `typescript.md` — strict mode, tipos compartilhados
- `conventions.md` — naming, ordenação, boas práticas

### `/standards/api/`
Camada de comunicação.
- `http-and-server-actions.md` — padrão de request, wrappers tipados
- `auth.md` — tokens, sessões, permissões

### Meta
- `README.md` — índice geral
- `changelog.md` — histórico de mudanças nas regras

---

## `/skills/` — Receitas executáveis

Recipes autocontidas para tarefas repetitivas. Substituem o standard na conta do context budget.

- `README.md` — catálogo de skills
- `approved/` — skills aprovadas para uso em produção
  - `form-page.md` — passo a passo para criar Form.tsx
  - `filter-page.md` — passo a passo para criar Filter.tsx
- `form-page/` e `filter-page/` — skills detalhadas com templates de código em `references/`
- `templates/skill.template.md` — template para propor nova skill

---

## `/workflows/` — Processos operacionais

Como o trabalho é executado (não padrões).

- `README.md` — catálogo
- `feature-delivery.md` — fases: Planning → Execution → Verification → Post-delivery
- `code-review.md` — checklist de revisão
- `testing.md` — matriz de testes por tipo
- `postmortem-loop.md` — loop de aprendizado pós-entrega

---

## `/docs/` — Documentação de APIs

**Regra congelada**: todo endpoint usado no código DEVE estar documentado aqui. Sem doc, Claude **não** inventa endpoint — pergunta ao usuário.

Um arquivo por recurso (ex.: `contracts.md`, `service-products.md`) com:
- Base URL
- Lista de operações (GET/POST/PUT/PATCH/DELETE)
- Payloads de exemplo
- Lookups utilizados

---

## `/templates/` — Boilerplates

Templates para criar documentos novos.

- `plan.template.md` — plano de entrega de feature
- `postmortem.template.md` — postmortem pós-entrega
- `adr.template.md` — Architecture Decision Record

---

## `/plans/` — Planos ativos

Pasta dated (`YYYY-MM/`) com planos de execução de features em andamento. Gerados a partir de `templates/plan.template.md`.

Formato: `plans/2026-04/feature-name.md`

---

## `/layouts/` — Referência de UI (legacy)

HTML de referência do design system. Não é fonte da verdade para código — apenas referência visual.

---

## Arquivos externos relacionados (raiz do projeto)

Fora da `/ai/` mas essenciais para o bootstrap do Claude:

| Arquivo | Papel |
|---|---|
| `CLAUDE.md` | Orientação rápida na raiz. Claude lê ao abrir o projeto. Aponta para `ai/context-pack.md`. |
| `.claude/settings.json` | Permissões do harness (comandos bash permitidos, hooks, env vars). |
| `.claude/settings.local.json` | Overrides locais (não commitados). |

---

## Como replicar em outro projeto

Use `scripts/bootstrap-ai-to-project.mjs` na raiz deste projeto:

```bash
node scripts/bootstrap-ai-to-project.mjs <caminho-do-projeto-alvo>
```

O script:
1. Varre o projeto alvo (package.json, tsconfig, estrutura de src/)
2. Copia as partes **genéricas** (agents, workflows, templates, governance, FOLDER-GUIDE)
3. Gera arquivos **tailored** (CLAUDE.md, context-pack.md, tech-stack.md, architecture.md) com base no que encontrou
4. Cria pastas vazias para `docs/` e `plans/`
5. Imprime relatório do que foi detectado e criado

Use `--force` para sobrescrever um `ai/` existente e `--dry-run` para só ver o que seria feito.
