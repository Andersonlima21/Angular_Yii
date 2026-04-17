# AI — Entrypoint

Claude lê este arquivo primeiro em toda sessão.

## Context Budget (regra congelada)

Carregar por sessão: **`context-pack.md`** + **1 standard** + **1 skill ou plano**. Não carregar tudo de uma vez.

## O que ler primeiro

1. **Este arquivo** — navegação e budget
2. **`context-pack.md`** — regras congeladas obrigatórias em toda sessão
3. **1 standard** sob demanda (ver `/standards/`)
4. **1 skill ou plano** sob demanda (ver `/skills/` ou `/plans/`)

## Índice de padrões (`/standards/`)

| Arquivo | Quando carregar |
|---|---|
| `domain/architecture.md` | Estrutura de pastas, fluxo de dados |
| `domain/tech-stack.md` | Dependências e versões |
| `domain/features.md` | Anatomia de um módulo de feature |
| `frontend/components.md` | Catálogo de componentes compartilhados |
| `frontend/styling.md` | Bootstrap 5.3.3, padrões visuais |
| `frontend/routing.md` | UI-Router, estados, resolves |
| `frontend/conventions.md` | Naming, ordenação, boas práticas |
| `api/http-and-server-actions.md` | Padrão de request, unwrap do envelope |
| `api/auth.md` | Tokens, sessões (futuro) |

## Índice de agentes (`/agents/`)

Ver `agents/README.md` para o mapa task → agente.

## Índice de workflows (`/workflows/`)

| Arquivo | Quando usar |
|---|---|
| `feature-delivery.md` | Entregar uma feature do zero |
| `code-review.md` | Revisar um PR |
| `testing.md` | Decidir que tipo de teste escrever |
| `postmortem-loop.md` | Após incidente ou entrega com problema |

## Documentação de API (`/docs/`)

Um arquivo por recurso. Todo endpoint usado no código DEVE estar documentado aqui.
Se não estiver documentado, **não inventar** — perguntar ao usuário.

## Regra de conflito

Se `ai/context-pack.md` conflitar com `CLAUDE.md`, **`context-pack.md` prevalece**.
`CLAUDE.md` é orientação rápida de onboarding; `context-pack.md` é a fonte da verdade congelada.
