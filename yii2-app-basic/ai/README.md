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
| `backend/controllers.md` | CORS, validação, envelope, routing |
| `backend/services.md` | Query Builder vs ActiveRecord, transações, timestamps |
| `backend/models.md` | Rules, behaviors, relacionamentos, scopes |
| `backend/testing.md` | Codeception unit + functional |

## Índice de agentes (`/agents/`)

Ver `agents/README.md` para o mapa task → agente.

## Índice de workflows (`/workflows/`)

| Arquivo | Quando usar |
|---|---|
| `feature-delivery.md` | Entregar uma feature do zero |
| `testing.md` | Decidir que tipo de teste escrever |
| `postmortem-loop.md` | Após incidente ou entrega com problema |

## Documentação de API (`/docs/`)

Um arquivo por recurso. Documenta o contrato de cada endpoint implementado.
Se não estiver documentado, **não inventar** — verificar o controller em `controllers/`.

## Regra de conflito

Se `ai/context-pack.md` conflitar com `CLAUDE.md`, **`context-pack.md` prevalece**.
`CLAUDE.md` é orientação rápida de onboarding; `context-pack.md` é a fonte da verdade congelada.
