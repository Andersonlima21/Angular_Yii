# Governance — Protocolo de Mudança de Regras

## O que é congelado

Qualquer regra em `context-pack.md` é **congelada**. Ela só muda via este protocolo.

## Quando uma regra pode mudar

- Mudança de banco (ex.: migrar de SQLite para PostgreSQL)
- Mudança de contrato de API (ex.: `POST /user-api` passa a retornar o recurso criado)
- Decisão arquitetural que invalida um anti-padrão existente
- Workaround resolvido (ex.: backend passa a retornar id no POST → remover workaround do frontend)
- Adição de autenticação que muda o fluxo de CORS ou headers

## Protocolo de mudança

1. **Propor** — descrever a mudança e o motivo em `ai/plans/YYYY-MM/` ou em conversa
2. **Revisar** — `tech-lead` valida impacto em backend e no frontend consumidor
3. **Aplicar** — atualizar `context-pack.md` + `standards/changelog.md`
4. **Comunicar** — registrar em `ai/standards/changelog.md`

## Matriz de ownership

| Área | Responsável |
|---|---|
| Envelope de API `{ success, type, data }` | Backend é fonte da verdade |
| Dois estilos de service (QB + AR) | `tech-lead` aprova unificação (só se projeto deixar de ser estudo) |
| CORS per-controller | `tech-lead` aprova mover para global |
| Timestamps SQLite | `database-architect` aprova mudança de formato |
| Anti-padrões | `tech-lead` aprova exceções pontuais |

## O que NÃO precisa de governance

- Adicionar novo controller REST (não muda regra existente)
- Criar nova migration (não muda regra existente)
- Adicionar endpoint `_new` (ActiveRecord espelho)
- Criar novo documento em `ai/plans/` ou `ai/docs/`
- Atualizar mapa de endpoints em `qa-engineer.md`
