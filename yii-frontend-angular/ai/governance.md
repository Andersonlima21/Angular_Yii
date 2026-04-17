# Governance — Protocolo de Mudança de Regras

## O que é congelado

Qualquer regra em `context-pack.md` é **congelada**. Ela só muda via este protocolo.

## Quando uma regra pode mudar

- Mudança de stack (ex.: migrar de AngularJS para outro framework)
- Mudança de contrato de API (ex.: backend passa a retornar id no POST)
- Decisão arquitetural que invalida um anti-padrão existente
- Workaround resolvido (ex.: backend implementa o id no POST → remover workaround)

## Protocolo de mudança

1. **Propor** — descrever a mudança e o motivo em `ai/plans/YYYY-MM/` ou em conversa
2. **Revisar** — tech-lead valida impacto em ambas as camadas
3. **Aplicar** — atualizar `context-pack.md` + `changelog.md`
4. **Comunicar** — registrar em `standards/changelog.md`

## Matriz de ownership

| Área | Responsável |
|---|---|
| Envelope de API | Backend (Yii2) é fonte da verdade |
| Estados UI-Router | tech-lead aprova adição/remoção |
| Workarounds | tech-lead + qa-engineer validam remoção |
| Anti-padrões | tech-lead aprova exceções |
| Ordem de scripts | frontend-developer + tech-lead |

## O que NÃO precisa de governance

- Adicionar novo state UI-Router (não muda regra existente)
- Adicionar novo service/component (não muda regra existente)
- Criar novo endpoint no backend (só atualizar `ai/docs/`)
- Criar novo documento em `ai/plans/` ou `ai/docs/`
