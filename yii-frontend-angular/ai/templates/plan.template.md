# Plano: [Nome da Feature]

**Data**: YYYY-MM-DD
**Agente responsável**: [tech-lead / frontend-developer / etc.]
**Status**: [ ] Planning | [ ] Execution | [ ] Verification | [ ] Done

---

## Objetivo

Uma frase descrevendo o que esta feature entrega ao usuário.

## Impacto por camada

| Camada | O que muda |
|---|---|
| Frontend | ex.: novo estado UI-Router, novo componente |
| Backend | ex.: novo endpoint, mudança de shape de resposta |
| Contrato de API | ex.: sim / não — descrever se sim |

## Critérios de aceite

- [ ] ...
- [ ] ...

---

## Fases

### Planning
- [ ] Requisitos levantados
- [ ] Impacto em frontend e backend mapeado
- [ ] Contrato de API definido (atualizar `ai/docs/` se necessário)

### Execution
- [ ] Backend implementado
- [ ] Frontend implementado
- [ ] `node --check app/**/*.js` sem erros

### Verification
- [ ] Testado manualmente no browser
- [ ] Casos de edge testados (ver `qa-engineer`)
- [ ] Sem regressões nos fluxos existentes

### Post-delivery
- [ ] `ai/docs/` atualizado se endpoint foi criado/alterado
- [ ] Workarounds resolvidos removidos de `context-pack.md` (se aplicável)
- [ ] Postmortem criado se houve incidente

---

## Handoff (preencher ao pausar)

**Parou em**: [descrição exata do ponto de parada]
**Próxima ação**: [primeira coisa a fazer ao retomar]
**Arquivos modificados**: [lista]
**Decisões tomadas**: [lista com motivos]
