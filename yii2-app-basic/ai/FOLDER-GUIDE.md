# AI Folder Guide

Este diretório é a **base de conhecimento** que o Claude Code (e outros agentes de IA) usam para trabalhar com eficiência neste codebase. Cada subpasta tem um papel bem definido — leia este guia antes de adicionar/mover conteúdo.

## Princípios

- **Context budget**: agentes carregam no máximo `context-pack.md` + **1 standard** + **1 skill/plano** por sessão
- **Frozen rules**: regras congeladas só mudam via governance (ver `governance.md`)
- **Patterns vs processes vs tasks**:
  - padrões estáveis → `standards/`
  - processos operacionais → `workflows/`
  - receitas executáveis → `skills/`
- **Precedência de conflito**: `context-pack.md` > `governance.md` > `README.md` > `CLAUDE.md` > demais docs
- **DB é gerenciado externamente**: sem migrations/factories/seeders neste repo. DDL vem de fora; schema é consultado via MCP

---

## Raiz

| Arquivo | Papel |
|---|---|
| `README.md` | **Entrypoint** — navegação, quick start, índice de standards/modules/skills/workflows/agents. Claude lê este arquivo primeiro. |
| `context-pack.md` | **Regras congeladas** carregadas em TODA sessão. Arquitetura em camadas (Request → Controller → Service → Model), multi-tenant, ILIKE vs LIKE, DB::transaction, anti-patterns. |
| `governance.md` | Protocolo de freeze/unfreeze de regras. Quem aprova mudança, como registrar, quando destravar. |
| `FOLDER-GUIDE.md` | Este arquivo. |

---

## `/agents/` — Papéis de especialistas

Cada arquivo define um agente com contexto, responsabilidades e padrões específicos. Espelhado em `.claude/agents/` para consumo pelo Claude Code.

| Agente | Quando usar                                                                                                               |
|---|---------------------------------------------------------------------------------------------------------------------------|
| `backend-developer.md` | Implementar Controllers, Services e lógica de negócio (Framework encontrado - stack encontrada)                           |
| `api-designer.md` | Definir contratos REST, endpoints, envelope de respostas                                                                  |
| `database-architect.md` | Projetar Models, relacionamentos, schema PostgreSQL (schema `wms`)                                                        |
| `qa-engineer.md` | Form Requests, validações e testes automatizados                                                                          |
| `code-reviewer.md` | Revisar código contra padrões e arquitetura                                                                               |
| `tech-lead.md` | Decisões técnicas, arquitetura de features                                                                                |
| `tech-lead-reviewer.md` | Aprovar planos, código e docs antes de merge                                                                              |
| `solutions-architect.md` | Arquitetura de plataforma, fases/gates, trade-offs                                                                        |
| `platform-sre.md` | Operação de gateway/infra, SLOs, go-live readiness                                                                        |
| `cli-skill-engineer.md` | Governança de MCP, skills e documentação CLI-agnostic                                                                     |
| `ui-ux-designer.md` | Auditar e criar protótipos UI/UX                                                                                          |
| `wms-domain-expert.md` | Domínio WMS: vocabulário, schema, regras, regulamentações ("o que existe / o que é correto")                              |
| `wms-operations-analyst.md` | Operações WMS: fluxos reais, linguagem de UI, priorização para operador logístico ("como alguém usa / o que precisa ver") |
| `handoffs.md` | Protocolo de handoff entre agentes                                                                                        |
| `README.md` | Catálogo e mapa task → agente                                                                                             |

---

## `/standards/` — Regras congeladas por domínio

Padrões estáveis organizados por camada. Agente carrega **1 por sessão** sob demanda.

### `/standards/api/`
Camada HTTP.
- `controllers.md` — responsabilidades de Controller, delegação pura a Service
- `services.md` — lógica de negócio, DB::transaction, audit logging
- `form-requests.md` — validação de entrada, `buildData()`, TrimStrings OFF
- `api-responses.md` — envelope `{success, message, data, meta}`
- `error-handling.md` — `DomainException`, códigos `{ENTITY}_{FIELD}_{ERROR_TYPE}`, `lang/pt_BR/errors.php`
- `service-reuse-patterns.md` — traits em `Services/Concerns/` (HasCrudOperations, HasActivationToggle, HasAuditTrail, HasListFiltering, HasUniquenessValidation)

### `/standards/domain/`
Camada de negócio.
- `architecture.md` — visão geral Request → Controller → Service → Model → DB
- `models.md` — Eloquent, relacionamentos, scopes, `TenantModel`
- `multi-tenant.md` — `company_id` via GlobalScope + hook `creating`, resolução de tenant por middleware
- `enums.md` — padrão de enums PHP e EnumLabel helper

### `/standards/testing/`
- `feature-tests.md` — padrão obrigatório de PHPUnit feature tests (sem factories, `Model::create([...])` explícito, traits em `tests/Support/Traits/`)

### `/standards/docs/`
- `ui-spec.md` — como escrever `frontend-ui-spec.md` por módulo
- `ui-prototype.md` — como gerar protótipos HTML interativos
- `analyst-docs.md` — documentação de domínio e análise

### Meta
- `README.md` — índice geral

---

## `/skills/` — Receitas executáveis

Recipes autocontidas para tarefas repetitivas. Substituem o standard na conta do context budget.

- `README.md` — router: "qual skill carregar dado o que estou implementando?"
- `governance.md` — regras para criar, atualizar e deprecar skills
- `approved/` — skills aprovadas para uso em produção
  - `crud-controller.md` — passo a passo de Controller (index, show, store, update, updateActive)
  - `crud-service.md` — passo a passo de Service (list, create, update, setActive)
  - `crud-formrequest.md` — FormRequests (Index, Store, Update, SetActive)
  - `api-response-error.md` — envelope JSON, códigos de erro, ListMetaFactory
  - `enum-patterns.md` — endpoint de lookup de enum + EnumLabel
  - `audit-plan.md` — auditoria pós-implementação
  - `frontend-design.md` — protótipos HTML/CSS de telas admin
- `templates/skill.template.md` — template para propor nova skill

---

## `/workflows/` — Processos operacionais

Como o trabalho é executado (não padrões).

- `README.md` — catálogo
- `feature-delivery.md` — Planning → Execution → Verification → Post-delivery
- `code-review.md` — checklist de revisão
- `testing.md` — matriz de testes
- `db-ddl-change.md` — processo de mudança de schema (MCP read-only; DDL é executado pelo usuário)
- `post-implementation-audit.md` — auditoria após entrega

---

## `/modules/` — Documentação viva por módulo

Source of truth de cada domínio implementado. Carregado **só** quando o módulo é tocado.

Estrutura por módulo: `README.md` (entrypoint) + `system/business-rules.md` + `system/endpoints-implemented.md`.

| Módulo | Escopo |
|---|---|
| `platform-auth/` | Auth V1 — login/refresh/me/logout, ChannelAbilityResolver, canal `wms-catalog` |
| `users/` | CRUD usuários + profile-types + roles + permissions tree |
| `roles/` | CRUD roles + permissões + listagem de profile-types |
| `companies/` | CRUD empresas admin (tenants do sistema) |
| `lookups/` | `GET /lookups/{slug}` — 26 lookup tables WMS, cache 24h |
| `service-products/` | Catálogo comercial (CRUD + configs template 1:1 atômico) |
| `customer-contracts/` | Contratos + sub-recurso services (configs override + configs_effective) |

---

## `/mcp/` — Governança de MCP

Define quais servidores MCP estão aprovados e políticas de uso.

- `README.md` — índice
- `approved/dbhub/` — DBHub (read-only para schema/constraints) com `config-examples/` e `security.md`
- `candidates/` — servidores em avaliação (brave-search, context7, github-mcp-server, postgres-mcp-pro, sentry-mcp, sequential-thinking, server-memory, server-postgres-deprecated)
- `evaluation.md` — critérios de avaliação
- `tool-policies.md` — políticas por tool (read-only, allowlist, rate limit)
- `lock/` — `mcp.lock.json` (estado aprovado) + schema

**Regra**: MCP é read-only para INSERT/UPDATE/DDL — essas operações são executadas manualmente pelo usuário.

---

## `/specs/` — Especificações funcionais + SQL

Specs de escopo antes da implementação, e scripts SQL ad-hoc.

- `wms-audit-fix-company-id.sql` — patch de tenant em auditoria
- `wms-cross-tenant-protection.sql` — hardening de proteção cross-tenant
- `warehouses/`, `products/`, `customers-and-contracts/` — specs por domínio

---

## `/postman/` — Convenções e coleções Postman

Coleções de teste manual e convenções de environment (BASE_URL, SUBDOMAIN, headers).

---

## `/templates/` — Boilerplates

Templates para criar documentos novos.

- `adr.template.md` — Architecture Decision Record
- `plan.template.md` — plano de entrega de feature
- `postmortem.template.md` — postmortem pós-entrega/incidente
- `module-docs.template.md` — README + business-rules + endpoints de um módulo novo
- `completion-checklist.template.md` — checklist de conclusão
- `fork-readme.template.md` — README para fork/spin-off

---

## `/plans/` — Planos ativos

Pasta dated (`YYYY-MM/`) com planos de execução em andamento. Gerados a partir de `templates/plan.template.md`.

Formato: `plans/2026-04/feature-name.md`.

---

## `/backlogs/` — Pendências futuras

Ideias e pendências que ainda não viraram plano aprovado.

---

## `/postmortems/` — Postmortems

Postmortems de incidentes e entregas que ensinaram lições. Gerados a partir de `templates/postmortem.template.md`.

---

## `/evaluation/` — Checklists de review

- `tech-lead-checklist.md` — checklist de aprovação de plano/código
- `lock-template.md` — template de lock de decisão
- `README.md` — índice

---

## `/scripts/` — Scripts utilitários

Automação local executável.

- `check-use-imports.php` — verifica se `use` statements cobrem todas as classes referenciadas via `::class` e injetadas no construtor
- `copy-claude-assets.ps1` — bootstrap do kit Claude num projeto destino (do zero, via varredura de stack)

---

## `/runtime/` — Estado transitório

Relatórios e estado gerados durante execução. Não é fonte da verdade — é saída de ferramentas.

---

## Arquivos externos relacionados (raiz do projeto)

Fora da `/ai/` mas essenciais para o bootstrap do Claude:

| Arquivo | Papel |
|---|---|
| `CLAUDE.md` | Orientação rápida na raiz. Claude lê ao abrir o projeto. Aponta para `ai/context-pack.md`. |
| `AGENTS.md` | Versão equivalente para outros agentes (Codex etc). |
| `.claude/agents/` | Agentes expostos ao Claude Code (espelha `ai/agents/`). |
| `.claude/commands/` | Slash commands (`/audit-plan`, `/frontend-design`, `/test`). |
| `.claude/settings.json` | Permissões do harness (comandos bash permitidos, hooks, env vars). |
| `.claude/settings.local.json` | Overrides locais (não commitados). |

---

## Como replicar em outro projeto

Use `ai/scripts/copy-claude-assets.ps1` (PowerShell — Windows):

```powershell
pwsh ai/scripts/copy-claude-assets.ps1 -Destination C:\caminho\projeto-destino
```

O script:
1. Varre o projeto alvo (composer.json, package.json, tsconfig, go.mod, Cargo.toml, Gemfile, pom.xml, Dockerfile, CI, etc.)
2. **Não copia conteúdo WMS** — gera tudo do zero
3. Cria a estrutura `ai/` + `.claude/` no destino
4. Gera `CLAUDE.md`, `ai/README.md`, `ai/context-pack.md`, `ai/governance.md` preenchidos com a stack detectada (e campos em branco para especializar)
5. Grava `ai/runtime/stack-detected.md` com o relatório cru da varredura

Use `-Force` para sobrescrever arquivos existentes no destino.
